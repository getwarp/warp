<?php

declare(strict_types=1);

namespace Warp\Container\ServiceProvider;

use Warp\Container\ContainerAwareInterface;

interface ServiceProviderInterface extends ContainerAwareInterface
{
    /**
     * Returns list of services provided by current provider.
     * @return string[]
     */
    public function provides(): array;

    /**
     * Use the register method to register items with the container.
     */
    public function register(): void;

    /**
     * Set a custom id for the service provider. This allows to register the same service provider multiple times.
     * @param string $id
     * @return self
     */
    public function setIdentifier(string $id): self;

    /**
     * The id of the service provider uniquely identifies it, so that we can quickly determine if it has already been
     * registered. Defaults to class name of provider.
     * @return string
     */
    public function getIdentifier(): string;
}
