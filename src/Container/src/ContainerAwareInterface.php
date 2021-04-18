<?php

declare(strict_types=1);

namespace spaceonfire\Container;

interface ContainerAwareInterface
{
    /**
     * Set a container.
     * @param ContainerInterface $container
     * @return void|$this returning $this is deprecated for this method. It would be void in next release.
     */
    public function setContainer(ContainerInterface $container);

    /**
     * Get the container.
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface;
}
