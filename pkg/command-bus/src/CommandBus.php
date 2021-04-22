<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus;

use spaceonfire\CommandBus\Exception\CannotInvokeHandlerException;
use spaceonfire\CommandBus\Mapping\CommandToHandlerMappingInterface;
use spaceonfire\Container\FactoryAggregateInterface;
use spaceonfire\Container\FactoryContainer;
use spaceonfire\Container\FactoryOptionsInterface;

/**
 * Receives a command and sends it through a chain of middleware for processing.
 */
class CommandBus
{
    private CommandToHandlerMappingInterface $mapping;

    private FactoryAggregateInterface $factory;

    private \Closure $middlewareChain;

    private bool $isClone = false;

    /**
     * CommandBus constructor.
     * @param CommandToHandlerMappingInterface $mapping
     * @param array<MiddlewareInterface|class-string<MiddlewareInterface>> $middleware
     * @param FactoryAggregateInterface|null $factory
     */
    public function __construct(
        CommandToHandlerMappingInterface $mapping,
        array $middleware = [],
        ?FactoryAggregateInterface $factory = null
    ) {
        $this->mapping = $mapping;
        $this->factory = $factory ?? new FactoryContainer();
        $this->middlewareChain = $this->makeMiddlewareChain($middleware);
    }

    /**
     * Clone command bus
     */
    public function __clone()
    {
        $middlewareChain = $this->middlewareChain->bindTo($this);
        \assert($middlewareChain instanceof \Closure);
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
     * Creates handler object by given class name using factory.
     *
     * You can modify this procedure in your successor class.
     *
     * @template T of object
     * @param class-string<T> $handlerClass
     * @param FactoryOptionsInterface|array<string,mixed>|null $options
     * @return T
     */
    protected function makeHandlerObject(string $handlerClass, $options = null): object
    {
        return $this->factory->make($handlerClass, $options);
    }

    /**
     * @param array<MiddlewareInterface|class-string<MiddlewareInterface>> $middlewareList
     * @return \Closure
     */
    private function makeMiddlewareChain(array $middlewareList): \Closure
    {
        $lastCallable = fn (object $command) => $this->makeCommandHandler($command)($command);

        while ($item = \array_pop($middlewareList)) {
            try {
                $middleware = \is_string($item) ? $this->factory->make($item) : $item;

                if (!$middleware instanceof MiddlewareInterface) {
                    throw new \InvalidArgumentException('Middleware should implement proper interface.');
                }
            } catch (\Throwable $e) {
                throw new \InvalidArgumentException(
                    \sprintf('Invalid middleware: %s.', \is_string($item) ? $item : \get_debug_type($item)),
                    $e->getCode(),
                    $e,
                );
            }

            $lastCallable = function ($command) use ($middleware, $lastCallable) {
                $lastCallable = $this->isClone ? \Closure::bind($lastCallable, $this) : $lastCallable;
                return $middleware->execute($command, $lastCallable);
            };
        }

        return $lastCallable;
    }

    /**
     * @param object $command
     * @return callable
     */
    private function makeCommandHandler(object $command): callable
    {
        $commandClass = \get_class($command);
        $handlerClass = $this->mapping->getClassName($commandClass);
        $handlerMethod = $this->mapping->getMethodName($commandClass);

        $handler = [$this->makeHandlerObject($handlerClass), $handlerMethod];

        if (!\is_callable($handler)) {
            throw CannotInvokeHandlerException::methodNotExists($command, $handlerClass, $handlerMethod);
        }

        return $handler;
    }
}
