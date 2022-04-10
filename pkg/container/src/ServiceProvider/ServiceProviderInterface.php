<?php

declare(strict_types=1);

namespace Warp\Container\ServiceProvider;

use Warp\Container\ContainerAwareInterface;

interface ServiceProviderInterface extends ContainerAwareInterface
{
    /**
     * The id of the service provider uniquely identifies it, so that we can quickly determine if it has already been
     * registered. Defaults to class name of provider.
     * @return string
     */
    public function getId(): string;

    /**
     * Returns list of services provided by current provider.
     * @return string[]
     */
    public function provides(): iterable;

    /**
     * Use the register method to register items with the container.
     */
    public function register(): void;
}
