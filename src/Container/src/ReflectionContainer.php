<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use BadMethodCallException;
use spaceonfire\Collection\Collection;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Container\Argument\ArgumentResolver;
use spaceonfire\Container\Argument\ResolverInterface;
use spaceonfire\Container\Definition\DefinitionInterface;
use spaceonfire\Container\Reflection\ReflectionFactory;
use spaceonfire\Container\Reflection\ReflectionInvoker;

final class ReflectionContainer implements ContainerInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

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
     * ReflectionContainer constructor.
     */
    public function __construct()
    {
        $this->argumentResolver = new ArgumentResolver($this);
        $this->objectFactory = new ReflectionFactory($this->argumentResolver);
        $this->callableInvoker = new ReflectionInvoker($this->argumentResolver, $this);
        $this->setContainer($this);
    }

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container): ContainerAwareInterface
    {
        $this->container = $container;
        $this->argumentResolver->setContainer($container);
        $this->callableInvoker->setContainer($container);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return class_exists($id);
    }

    /**
     * @inheritDoc
     */
    public function get($id, array $arguments = [])
    {
        return $this->make($id, $arguments);
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
    public function add(string $id, $concrete = null, bool $shared = false): DefinitionInterface
    {
        throw new BadMethodCallException('ReflectionContainer does not support definitions');
    }

    /**
     * @inheritDoc
     */
    public function share(string $id, $concrete = null): DefinitionInterface
    {
        throw new BadMethodCallException('ReflectionContainer does not support definitions');
    }

    /**
     * @inheritDoc
     */
    public function hasTagged(string $tag): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getTagged(string $tag): CollectionInterface
    {
        return new Collection();
    }
}
