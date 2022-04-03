<?php

declare(strict_types=1);

namespace Warp\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Warp\Collection\CollectionInterface;
use Warp\Container\Definition\DefinitionInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * @inheritDoc
     * @param array<string,mixed> $arguments
     */
    public function get($id, array $arguments = []);

    /**
     * Build new instance of $alias
     * @param string $alias
     * @param array<string,mixed> $arguments
     * @return mixed
     */
    public function make(string $alias, array $arguments = []);

    /**
     * Invoke a callable via the container.
     * @param callable $callable
     * @param array<string,mixed> $arguments
     * @return mixed
     */
    public function invoke(callable $callable, array $arguments = []);

    /**
     * Register new definition.
     * @param string $id
     * @param mixed $concrete
     * @param boolean $shared
     * @return DefinitionInterface
     */
    public function add(string $id, $concrete = null, bool $shared = false): DefinitionInterface;

    /**
     * Proxy to add with shared as true.
     * @param string $id
     * @param mixed $concrete
     * @return DefinitionInterface
     */
    public function share(string $id, $concrete = null): DefinitionInterface;

    /**
     * @param string $tag
     * @return bool
     */
    public function hasTagged(string $tag): bool;

    /**
     * @param string $tag
     * @return mixed[]|CollectionInterface
     */
    public function getTagged(string $tag): CollectionInterface;
}
