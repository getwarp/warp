<?php

declare(strict_types=1);

namespace Warp\Container;

trait ContainerAwareTrait
{
    /**
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container instanceof ContainerInterface) {
            return $this->container;
        }

        throw new Exception\ContainerException('No container implementation has been set.');
    }
}
