<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus;

use Closure;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use spaceonfire\CommandBus\Mapping\CommandToHandlerMapping;
use Webmozart\Assert\Assert;
use function array_pop;
use function get_class;
use function is_callable;

/**
 * Receives a command and sends it through a chain of middleware for processing.
 */
class CommandBus
{
    /**
     * @var CommandToHandlerMapping
     */
    private $mapping;

    /**
     * @var Closure
     */
    private $middlewareChain;

    /**
     * @var ContainerInterface|null
     */
    private $container;

    /**
     * @var bool
     */
    private $isClone = false;

    /**
     * CommandBus constructor.
     * @param CommandToHandlerMapping $mapping
     * @param Middleware[] $middleware
     * @param ContainerInterface|null $container
     */
    public function __construct(
        CommandToHandlerMapping $mapping,
        array $middleware = [],
        ?ContainerInterface $container = null
    ) {
        foreach ($middleware as $m) {
            if (!$m instanceof Middleware) {
                throw new InvalidArgumentException(sprintf(
                    'Argument $middleware must be an array of %s. Got one %s',
                    Middleware::class,
                    get_class($m)
                ));
            }
        }

        $this->mapping = $mapping;
        $this->container = $container;
        $this->middlewareChain = $this->createExecutionChain($middleware);
    }

    /**
     * Clone command bus
     */
    public function __clone()
    {
        $middlewareChain = Closure::bind($this->middlewareChain, $this);
        Assert::isCallable($middlewareChain);
        $this->middlewareChain = $middlewareChain;
        $this->isClone = true;
    }

    /**
     * Executes the given command and optionally returns a value
     * @param object $command
     * @return mixed
     */
    public function handle(object $command)
    {
        return ($this->middlewareChain)($command);
    }

    /**
     * Creates handler object by given class name.
     *
     * It uses container if one passed to command bus or simply call handler class constructor.
     * You can patch this procedure in your own implementation of command bus.
     *
     * @param string $handlerClassName
     * @return object
     */
    protected function createHandlerObject(string $handlerClassName): object
    {
        if ($this->container && $this->container->has($handlerClassName)) {
            return $this->container->get($handlerClassName);
        }

        return new $handlerClassName();
    }

    /**
     * @param Middleware[] $middlewareList
     * @return Closure
     */
    private function createExecutionChain(array $middlewareList): Closure
    {
        $lastCallable = function (object $command) {
            return $this->createCommandHandler($command)($command);
        };

        while ($middleware = array_pop($middlewareList)) {
            $lastCallable = function ($command) use ($middleware, $lastCallable) {
                $lastCallable = $this->isClone ? Closure::bind($lastCallable, $this) : $lastCallable;
                return $middleware->execute($command, $lastCallable);
            };
        }

        return $lastCallable;
    }

    /**
     * @param object $command
     * @return callable
     */
    private function createCommandHandler(object $command): callable
    {
        $commandClassName = get_class($command);
        $handlerClassName = $this->mapping->getClassName($commandClassName);
        $methodName = $this->mapping->getMethodName($commandClassName);

        $handler = $this->createHandlerObject($handlerClassName);

        if (!is_callable([$handler, $methodName])) {
            throw CanNotInvokeHandler::forCommand(
                $command,
                sprintf('Method "%s" does not exist on handler', $methodName)
            );
        }

        return [$handler, $methodName];
    }
}
