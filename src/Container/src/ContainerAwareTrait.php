<?php

declare(strict_types=1);

namespace spaceonfire\Container;

trait ContainerAwareTrait
{
    /**
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * Set a container
     * @param ContainerInterface $container
     * @return $this|ContainerAwareInterface
     */
    public function setContainer(ContainerInterface $container): ContainerAwareInterface
    {
        $this->container = $container;
        return $this;
    }

    /**
     * Get the container
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container instanceof ContainerInterface) {
            return $this->container;
        }

        throw new Exception\ContainerException('No container implementation has been set.');
    }
}
