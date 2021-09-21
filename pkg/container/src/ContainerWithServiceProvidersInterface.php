<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use spaceonfire\Container\ServiceProvider\ServiceProviderInterface;

interface ContainerWithServiceProvidersInterface extends ContainerInterface
{
    /**
     * Add a service provider.
     * @param ServiceProviderInterface|string|mixed $provider
     * @return $this|self
     */
    public function addServiceProvider($provider): self;
}
