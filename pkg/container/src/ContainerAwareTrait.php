<?php

declare(strict_types=1);

namespace Warp\Container;

use Psr\Container\ContainerInterface;

trait ContainerAwareTrait
{
    protected ?ContainerInterface $container = null;

    public function setContainer(?ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function getContainer(): ContainerInterface
    {
        if (null === $this->container) {
            throw new Exception\ContainerException('No container implementation has been set.');
        }

        return $this->container;
    }
}
