<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus;

use Closure;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use spaceonfire\CommandBus\Mapping\CommandToHandlerMapping;
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
     * @var callable
     */
    private $middlewareChain;
    /**
     * @var ContainerInterface
     */
    private $container;

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
     * Executes the given command and optionally returns a value
     * @param object $command
     * @return mixed
     */
    public function handle(object $command)
    {
        return ($this->middlewareChain)($command);
    }

    /**
     * @param Middleware[] $middlewareList
     * @return callable
     */
    private function createExecutionChain(array $middlewareList): callable
    {
        $lastCallable = function (object $command) {
            return $this->createCommandHandler($command)($command);
        };

        while ($middleware = array_pop($middlewareList)) {
            $lastCallable = static function ($command) use ($middleware, $lastCallable) {
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
                'Method ' . $methodName . ' does not exist on handler'
            );
        }

        return [$handler, $methodName];
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
        return $this->container
            ? $this->container->get($handlerClassName)
            : new $handlerClassName();
    }

    /**
     * Clone command bus
     */
    public function __clone()
    {
        $this->middlewareChain = Closure::bind($this->middlewareChain, $this);
    }
}
