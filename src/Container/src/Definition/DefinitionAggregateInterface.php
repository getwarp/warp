<?php

declare(strict_types=1);

namespace spaceonfire\Container\Definition;

use IteratorAggregate;
use spaceonfire\Container\ContainerInterface;

interface DefinitionAggregateInterface extends IteratorAggregate
{
    /**
     * Add a definition to the aggregate.
     * @param DefinitionInterface $definition
     * @return $this
     */
    public function addDefinition(DefinitionInterface $definition): self;

    /**
     * Checks whether alias exists as definition.
     * @param string $id
     * @return boolean
     */
    public function hasDefinition(string $id): bool;

    /**
     * Get the definition to be extended.
     * @param string $id
     * @return DefinitionInterface
     */
    public function getDefinition(string $id): DefinitionInterface;

    /**
     * Make definition
     * @param string $abstract
     * @param mixed $concrete
     * @param bool $shared
     * @return DefinitionInterface
     */
    public function makeDefinition(string $abstract, $concrete, bool $shared = false): DefinitionInterface;

    /**
     * Resolve and build a concrete value from an id/alias.
     * @param string $id
     * @param ContainerInterface $container
     * @return mixed
     */
    public function resolve(string $id, ContainerInterface $container);
}
