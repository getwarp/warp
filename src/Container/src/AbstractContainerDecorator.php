<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use spaceonfire\Container\Definition\DefinitionInterface;
use spaceonfire\Container\Exception\ContainerException;

abstract class AbstractContainerDecorator implements ContainerWithServiceProvidersInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AbstractContainerDecorator constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function get($id, array $arguments = [])
    {
        return $this->container->get($id, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return $this->container->has($id);
    }

    /**
     * @inheritDoc
     */
    public function add(string $id, $concrete = null, bool $shared = false): DefinitionInterface
    {
        return $this->container->add($id, $concrete, $shared);
    }

    /**
     * @inheritDoc
     */
    public function share(string $id, $concrete = null): DefinitionInterface
    {
        return $this->container->share($id, $concrete);
    }

    /**
     * @inheritDoc
     */
    public function make(string $alias, array $arguments = [])
    {
        return $this->container->make($alias, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function invoke(callable $callable, array $arguments = [])
    {
        return $this->container->invoke($callable, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function addServiceProvider($provider): ContainerWithServiceProvidersInterface
    {
        if ($this->container instanceof ContainerWithServiceProvidersInterface) {
            $this->container->addServiceProvider($provider);
            return $this;
        }

        throw new ContainerException('Provided container does not support service providers');
    }
}
