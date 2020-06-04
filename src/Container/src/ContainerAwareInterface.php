<?php

declare(strict_types=1);

namespace spaceonfire\Container;

interface ContainerAwareInterface
{
    /**
     * Set a container
     * @param ContainerInterface $container
     * @return $this|ContainerAwareInterface
     */
    public function setContainer(ContainerInterface $container): self;

    /**
     * Get the container
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface;
}
