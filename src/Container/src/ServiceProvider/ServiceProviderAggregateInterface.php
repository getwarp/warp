<?php

declare(strict_types=1);

namespace spaceonfire\Container\ServiceProvider;

use IteratorAggregate;
use spaceonfire\Container\ContainerAwareInterface;

interface ServiceProviderAggregateInterface extends ContainerAwareInterface, IteratorAggregate
{
    /**
     * Add a service provider to the aggregate.
     * @param ServiceProviderInterface $provider
     * @return $this|ServiceProviderAggregateInterface
     */
    public function addProvider(ServiceProviderInterface $provider): self;

    /**
     * Determines whether a service is provided by the aggregate.
     * @param string $service
     * @return boolean
     */
    public function provides(string $service): bool;

    /**
     * Invokes the register method of a provider that provides a specific service.
     * @param string $service
     */
    public function register(string $service): void;
}
