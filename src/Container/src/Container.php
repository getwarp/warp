<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use InvalidArgumentException;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Container\Argument\ArgumentResolver;
use spaceonfire\Container\Argument\ResolverInterface;
use spaceonfire\Container\Definition\DefinitionAggregate;
use spaceonfire\Container\Definition\DefinitionAggregateInterface;
use spaceonfire\Container\Definition\DefinitionInterface;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\Exception\NotFoundException;
use spaceonfire\Container\Reflection\ReflectionFactory;
use spaceonfire\Container\Reflection\ReflectionInvoker;
use spaceonfire\Container\ServiceProvider\ServiceProviderAggregate;
use spaceonfire\Container\ServiceProvider\ServiceProviderAggregateInterface;
use spaceonfire\Container\ServiceProvider\ServiceProviderInterface;

final class Container implements ContainerWithServiceProvidersInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var DefinitionAggregateInterface
     */
    private $definitions;
    /**
     * @var ServiceProviderAggregateInterface
     */
    private $providers;
    /**
     * @var ResolverInterface
     */
    private $argumentResolver;
    /**
     * @var ReflectionFactory
     */
    private $objectFactory;
    /**
     * @var ReflectionInvoker
     */
    private $callableInvoker;

    /**
     * Container constructor.
     * @param DefinitionAggregateInterface|null $definitions
     * @param ServiceProviderAggregateInterface|null $providers
     */
    public function __construct(
        ?DefinitionAggregateInterface $definitions = null,
        ?ServiceProviderAggregateInterface $providers = null
    ) {
        $this->definitions = $definitions ?? new DefinitionAggregate();
        $this->providers = $providers ?? new ServiceProviderAggregate();
        $this->argumentResolver = new ArgumentResolver($this);
        $this->objectFactory = new ReflectionFactory($this->argumentResolver);
        $this->callableInvoker = new ReflectionInvoker($this->argumentResolver, $this);
        $this->setContainer($this);
    }

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        $this->argumentResolver->setContainer($container);
        $this->callableInvoker->setContainer($container);
        $this->providers->setContainer($container);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return $this->definitions->hasDefinition($id) || $this->providers->provides($id);
    }

    /**
     * @inheritDoc
     */
    public function add(string $id, $concrete = null, bool $shared = false): DefinitionInterface
    {
        $def = $this->definitions->makeDefinition($id, $concrete, $shared);
        $this->definitions->addDefinition($def);
        return $def;
    }

    /**
     * @inheritDoc
     */
    public function share(string $id, $concrete = null): DefinitionInterface
    {
        return $this->add($id, $concrete, true);
    }

    /**
     * @inheritDoc
     */
    public function addServiceProvider($provider): ContainerWithServiceProvidersInterface
    {
        if ($provider instanceof ServiceProviderInterface) {
            $this->providers->addProvider($provider);
            return $this;
        }

        if (is_string($provider)) {
            return $this->addServiceProvider($this->make($provider));
        }

        throw new InvalidArgumentException(sprintf('Unsupported provider type: %s', gettype($provider)));
    }

    /**
     * @inheritDoc
     */
    public function get($id, array $arguments = [])
    {
        if ($this->definitions->hasDefinition($id)) {
            return $this->definitions->resolve($id, $this->getContainer());
        }

        if ($this->providers->provides($id)) {
            $this->providers->register($id);

            if (!$this->definitions->hasDefinition($id) || $this->definitions->hasTag($id)) {
                throw new ContainerException(sprintf('Service provider lied about providing (%s) service', $id));
            }

            return $this->get($id, $arguments);
        }

        throw new NotFoundException(sprintf('Alias (%s) is not being managed by the container', $id));
    }

    /**
     * @inheritDoc
     */
    public function make(string $alias, array $arguments = [])
    {
        return ($this->objectFactory)($alias, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function invoke(callable $callable, array $arguments = [])
    {
        return ($this->callableInvoker)($callable, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function hasTagged(string $tag): bool
    {
        return $this->definitions->hasTag($tag);
    }

    /**
     * @inheritDoc
     */
    public function getTagged(string $tag): CollectionInterface
    {
        if ($this->providers->provides($tag)) {
            $this->providers->register($tag);
        }

        return $this->definitions->resolveTagged($tag, $this->getContainer());
    }
}
