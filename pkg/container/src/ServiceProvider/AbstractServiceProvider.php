<?php

declare(strict_types=1);

namespace spaceonfire\Container\ServiceProvider;

use Psr\Container\ContainerInterface;
use spaceonfire\Container\DefinitionAggregateInterface;
use spaceonfire\Container\Exception\ContainerException;

abstract class AbstractServiceProvider implements ServiceProviderInterface
{
    protected ?string $identifier = null;

    protected ?ContainerInterface $container = null;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface&DefinitionAggregateInterface
     */
    public function getContainer(): ContainerInterface
    {
        if (null === $this->container) {
            throw new ContainerException('No container implementation has been set.');
        }

        \assert($this->container instanceof DefinitionAggregateInterface);

        return $this->container;
    }

    public function getId(): string
    {
        return $this->identifier ?? static::class;
    }

    public function setId(string $id): void
    {
        $this->identifier = $id;
    }
}
