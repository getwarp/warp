<?php

declare(strict_types=1);

namespace spaceonfire\Container\ServiceProvider;

interface BootableServiceProviderInterface extends ServiceProviderInterface
{
    /**
     * Method will be invoked on registration of a service provider implementing
     * this interface. Provides ability for eager loading of Service Providers.
     */
    public function boot(): void;
}
