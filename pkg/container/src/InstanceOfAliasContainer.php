<?php

declare(strict_types=1);

namespace Warp\Container;

use Psr\Container\ContainerInterface;
use Warp\Container\Exception\NotFoundException;

final class InstanceOfAliasContainer implements
    ContainerInterface,
    GetWithOptionsMethodInterface,
    ContainerAwareInterface
{
    private ContainerInterface $container;

    private function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public static function wrap(ContainerInterface $container): self
    {
        if ($container instanceof self) {
            return $container;
        }

        return new self($container);
    }

    public function get(string $id, $options = null)
    {
        if ($this->container->has($id)) {
            return $this->container instanceof GetWithOptionsMethodInterface
                ? $this->container->get($id, $options)
                : $this->container->get($id);
        }

        if ($this->container instanceof $id) {
            return $this->container;
        }

        throw NotFoundException::alias($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id) || $this->container instanceof $id;
    }

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
