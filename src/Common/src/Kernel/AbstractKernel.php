<?php

declare(strict_types=1);

namespace spaceonfire\Common\Kernel;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use spaceonfire\Container\CompositeContainer;
use spaceonfire\Container\Container;
use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\ContainerWithServiceProvidersInterface;
use spaceonfire\Container\ReflectionContainer;
use spaceonfire\Container\ServiceProvider\ServiceProviderInterface;

abstract class AbstractKernel
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var bool
     */
    protected $debugModeEnabled;

    /**
     * AbstractKernel constructor.
     * @param ContainerWithServiceProvidersInterface|null $container
     * @param bool $debugModeEnabled
     */
    public function __construct(
        ?ContainerWithServiceProvidersInterface $container = null,
        bool $debugModeEnabled = false
    ) {
        if (null === $container) {
            $container = new CompositeContainer([
                50 => new Container(),
                100 => new ReflectionContainer(),
            ]);
        }

        $this->container = $container;
        $this->debugModeEnabled = $debugModeEnabled;

        $this->container->add(PsrContainerInterface::class, ContainerInterface::class);
        $this->container->add(ContainerInterface::class, [$this, 'getContainer']);

        $this->container->add('kernel', static::class);
        $this->container->share(static::class, $this);
        $this->container->add('kernel.debug', [$this, 'isDebugModeEnabled']);

        foreach ($this->loadServiceProviders() as $serviceProvider) {
            $this->container->addServiceProvider($serviceProvider);
        }
    }

    /**
     * Returns the container.
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Running debug mode?
     * @return bool
     */
    public function isDebugModeEnabled(): bool
    {
        return $this->debugModeEnabled;
    }

    protected function enableDebugMode(bool $debug): void
    {
        $this->debugModeEnabled = $debug;
    }

    /**
     * @return string[]|ServiceProviderInterface[]
     */
    protected function loadServiceProviders(): iterable
    {
        return [];
    }
}
