<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use Psr\Container\ContainerInterface;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\Exception\NotFoundException;

/**
 * @implements \IteratorAggregate<ContainerInterface>
 */
final class CompositeContainer implements
    ContainerInterface,
    GetWithOptionsMethodInterface,
    ContainerAwareInterface,
    ServiceProviderAggregateInterface,
    DefinitionAggregateInterface,
    FactoryAggregateInterface,
    InvokerInterface,
    \IteratorAggregate
{
    private ContainerInterface $rootContainer;

    /**
     * @template T
     * @var array<class-string<T>,array{T,int}>
     */
    private array $activeInstances = [];

    /**
     * @var array<int, ContainerInterface[]>
     */
    private array $containers = [];

    private int $lastPriority = 0;

    public function __construct(ContainerInterface ...$containers)
    {
        $this->setContainer($this);

        foreach ($containers as $container) {
            $this->addContainer($container);
        }
    }

    public function setContainer(ContainerInterface $container): void
    {
        $this->rootContainer = $container;

        foreach ($this->getIterator() as $delegate) {
            if ($delegate instanceof ContainerAwareInterface) {
                $delegate->setContainer($container);
            }
        }
    }

    public function getContainer(): ContainerInterface
    {
        return $this->rootContainer;
    }

    /**
     * Attaches a container to the composite container.
     * @param ContainerInterface $container
     * @param int|null $priority
     */
    public function addContainer(ContainerInterface $container, ?int $priority = null): void
    {
        $priority ??= $this->lastPriority + 10;
        $this->lastPriority = $priority > $this->lastPriority ? $priority : $this->lastPriority;

        $this->setActiveInstance($container, $priority);

        if ($container instanceof ContainerAwareInterface) {
            $container->setContainer($this->getContainer());
        }

        $this->containers[$priority] ??= [];
        $this->containers[$priority][] = $container;
    }

    /**
     * @return \Generator<ContainerInterface>
     */
    public function getIterator(): \Generator
    {
        // Sort by priority
        \ksort($this->containers);

        foreach ($this->containers as $containers) {
            foreach ($containers as $container) {
                yield $container;
            }
        }
    }

    public function get(string $id, $options = null)
    {
        if (isset($this->activeInstances[$id]) && $this instanceof $id) {
            return $this;
        }

        foreach ($this->getIterator() as $container) {
            if (!$container->has($id)) {
                continue;
            }

            return $container instanceof GetWithOptionsMethodInterface
                ? $container->get($id, $options)
                : $container->get($id);
        }

        throw NotFoundException::alias($id);
    }

    public function has(string $id): bool
    {
        if (isset($this->activeInstances[$id])) {
            return true;
        }

        foreach ($this->getIterator() as $container) {
            if ($container->has($id)) {
                return true;
            }
        }

        return false;
    }

    public function hasFactory(string $class): bool
    {
        return $this->getActiveInstance(FactoryAggregateInterface::class)->hasFactory($class);
    }

    public function getFactory(string $class): FactoryInterface
    {
        return $this->getActiveInstance(FactoryAggregateInterface::class)->getFactory($class);
    }

    public function make(string $class, $options = null)
    {
        return $this->getActiveInstance(FactoryAggregateInterface::class)->make($class, $options);
    }

    public function invoke(callable $callable, $options = null)
    {
        return $this->getActiveInstance(InvokerInterface::class)->invoke($callable, $options);
    }

    public function define(string $id, $concrete = null, bool $shared = false): DefinitionInterface
    {
        return $this->getActiveInstance(DefinitionAggregateInterface::class)->define($id, $concrete, $shared);
    }

    public function addServiceProvider($provider): void
    {
        $this->getActiveInstance(ServiceProviderAggregateInterface::class)->addServiceProvider($provider);
    }

    public function hasTagged(string $tag): bool
    {
        foreach ($this->getIterator() as $container) {
            if ($container instanceof DefinitionAggregateInterface && $container->hasTagged($tag)) {
                return true;
            }
        }

        return false;
    }

    public function getTagged(string $tag): \Generator
    {
        foreach ($this->getIterator() as $container) {
            if (!$container instanceof DefinitionAggregateInterface) {
                continue;
            }

            yield from $container->getTagged($tag);
        }
    }

    private function setActiveInstance(ContainerInterface $container, int $priority): void
    {
        $types = [
            ServiceProviderAggregateInterface::class,
            DefinitionAggregateInterface::class,
            FactoryAggregateInterface::class,
            InvokerInterface::class,
        ];

        foreach ($types as $type) {
            if (!$container instanceof $type) {
                continue;
            }

            [$currentInstance, $currentInstancePriority] = $this->activeInstances[$type] ?? [null, \INF];

            if (null !== $currentInstance && $currentInstancePriority < $priority) {
                continue;
            }

            $this->activeInstances[$type] = [$container, $priority];
        }
    }

    /**
     * @template T
     * @param class-string<T> $class
     * @return T
     */
    private function getActiveInstance(string $class)
    {
        [$instance] = $this->activeInstances[$class] ?? [null];

        if ($instance instanceof $class) {
            return $instance;
        }

        throw new ContainerException(\sprintf('No %s instance provided to composite container.', $class));
    }
}
