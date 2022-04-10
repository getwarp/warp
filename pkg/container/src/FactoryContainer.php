<?php

declare(strict_types=1);

namespace Warp\Container;

use Psr\Container\ContainerInterface;
use Warp\Container\Factory\Reflection\ReflectionFactoryAggregate;
use Warp\Container\Factory\Reflection\ReflectionInvoker;

final class FactoryContainer implements
    ContainerInterface,
    GetWithOptionsMethodInterface,
    ContainerAwareInterface,
    FactoryAggregateInterface,
    InvokerInterface
{
    private ContainerInterface $rootContainer;

    private FactoryAggregateInterface $factory;

    private InvokerInterface $invoker;

    public function __construct(?FactoryAggregateInterface $factory = null, ?InvokerInterface $invoker = null)
    {
        $this->factory = $factory ?? new ReflectionFactoryAggregate($this);
        $this->invoker = $invoker ?? new ReflectionInvoker($this);
        $this->setContainer($this);
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->hasFactory($id);
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @param FactoryOptionsInterface|array<string,mixed>|null $options
     * @return T
     */
    public function get(string $id, $options = null)
    {
        return $this->make($id, $options);
    }

    public function hasFactory(string $class): bool
    {
        return $this->factory->hasFactory($class);
    }

    public function getFactory(string $class): FactoryInterface
    {
        return $this->factory->getFactory($class);
    }

    public function make(string $class, $options = null)
    {
        return $this->factory->make($class, $options);
    }

    public function invoke(callable $callable, $options = null)
    {
        return $this->invoker->invoke($callable, $options);
    }

    public function setContainer(ContainerInterface $container): void
    {
        $this->rootContainer = $container;

        if ($this->factory instanceof ContainerAwareInterface) {
            $this->factory->setContainer($this->rootContainer);
        }

        if ($this->invoker instanceof ContainerAwareInterface) {
            $this->invoker->setContainer($this->rootContainer);
        }
    }

    public function getContainer(): ContainerInterface
    {
        return $this->rootContainer;
    }
}
