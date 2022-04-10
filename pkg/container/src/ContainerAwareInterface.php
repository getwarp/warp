<?php

declare(strict_types=1);

namespace Warp\Container;

use Psr\Container\ContainerInterface;

interface ContainerAwareInterface
{
    /**
     * Set a container.
     * @param ContainerInterface $container
     * @return void
     */
    public function setContainer(ContainerInterface $container): void;

    /**
     * Get a container.
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface;
}
