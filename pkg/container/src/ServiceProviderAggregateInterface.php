<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use spaceonfire\Container\ServiceProvider\ServiceProviderInterface;

interface ServiceProviderAggregateInterface
{
    /**
     * Add a service provider.
     * @param ServiceProviderInterface|class-string<ServiceProviderInterface> $provider
     */
    public function addServiceProvider($provider): void;
}
