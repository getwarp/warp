<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use Psr\Container\ContainerInterface;
use spaceonfire\Container\Factory\Definition;
use spaceonfire\Container\Factory\FactoryOptions;
use spaceonfire\Container\Factory\Reflection\ReflectionFactoryAggregate;
use spaceonfire\Container\Factory\Reflection\ReflectionInvoker;
use spaceonfire\Container\ServiceProvider\BootableServiceProviderInterface;
use spaceonfire\Container\ServiceProvider\ServiceProviderInterface;

final class DefinitionContainer implements
    ContainerInterface,
    GetWithOptionsMethodInterface,
    ContainerAwareInterface,
    ServiceProviderAggregateInterface,
    DefinitionAggregateInterface,
    FactoryAggregateInterface,
    InvokerInterface
{
    private ContainerInterface $rootContainer;

    private FactoryAggregateInterface $factory;

    private InvokerInterface $invoker;

    /**
     * @var array<string,Definition<mixed>>
     */
    private array $definitions = [];

    /**
     * @var array<string,ServiceProviderInterface>
     */
    private array $providers = [];

    /**
     * @var array<string,array<string,string>>
     */
    private array $providesMap;

    /**
     * @var array<string,true>
     */
    private array $definitionTags = [];

    public function __construct(?FactoryAggregateInterface $factory = null, ?InvokerInterface $invoker = null)
    {
        $this->factory = $factory ?? new ReflectionFactoryAggregate($this);
        $this->invoker = $invoker ?? new ReflectionInvoker($this);
        $this->setContainer($this);
    }

    public function addServiceProvider($provider): void
    {
        if (\is_string($provider)) {
            $provider = $this->make($provider);
        }

        if (!$provider instanceof ServiceProviderInterface) {
            throw new Exception\ContainerException(\sprintf(
                'Argument #1 ($provider) should be instance of %s. Got: %s.',
                ServiceProviderInterface::class,
                \get_debug_type($provider),
            ));
        }

        $provider->setContainer($this->rootContainer);

        if ($provider instanceof BootableServiceProviderInterface) {
            $provider->boot();
        }

        $providerId = $provider->getId();

        $this->providers[$providerId] = $provider;

        foreach ($provider->provides() as $service) {
            $this->providesMap[$service] ??= [];
            $this->providesMap[$service][$providerId] = $providerId;
        }
    }

    public function define(string $id, $concrete = null, bool $shared = false): DefinitionInterface
    {
        if (isset($this->definitions[$id])) {
            throw new Exception\ContainerException(\sprintf('Alias (%s) definition already defined.', $id));
        }

        $def = new Definition($id, $concrete, $shared);

        $this->definitions[$def->getId()] = $def;

        return $def;
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]) || isset($this->providesMap[$id]);
    }

    public function get(string $id, $options = null)
    {
        $this->tryRegisterService($id);

        if (!isset($this->definitions[$id])) {
            throw Exception\NotFoundException::alias($id);
        }

        $this->definitions[$id]->setContainer($this->getContainer());
        return $this->definitions[$id]->make(FactoryOptions::wrap($options));
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

    public function hasTagged(string $tag): bool
    {
        if (isset($this->providesMap[$tag])) {
            return true;
        }

        if (isset($this->definitionTags[$tag])) {
            return true;
        }

        foreach ($this->definitions as $definition) {
            foreach ($definition->getTags() as $definitionTag) {
                $this->definitionTags[$definitionTag] = true;
            }

            if (!$definition->hasTag($tag)) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function getTagged(string $tag): \Generator
    {
        $this->tryRegisterService($tag);

        foreach ($this->definitions as $offset => $definition) {
            if (!$definition->hasTag($tag)) {
                continue;
            }

            $definition->setContainer($this->getContainer());

            $this->definitions[$offset] = $definition;

            yield $definition->make();
        }
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

    private function tryRegisterService(string $id): void
    {
        $providerIds = $this->providesMap[$id] ?? [];

        foreach ($providerIds as $providerId) {
            $provider = $this->providers[$providerId] ?? null;

            if (null === $provider) {
                continue;
            }

            $provider->setContainer($this->rootContainer);

            $provider->register();

            unset($this->providers[$providerId]);
        }

        unset($providerIds, $this->providesMap[$id]);
    }
}
