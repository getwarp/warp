<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use PhpOption\Option;

interface DefinitionAggregateInterface
{
    /**
     * Register definition.
     * @template T
     * @param string|class-string<T> $id
     * @param T|Option<T>|string|class-string<T>|callable():T|null $concrete
     * @param boolean $shared
     * @return DefinitionInterface<T>
     */
    public function define(string $id, $concrete = null, bool $shared = false): DefinitionInterface;

    /**
     * @param string $tag
     * @return bool
     */
    public function hasTagged(string $tag): bool;

    /**
     * @param string $tag
     * @return \Generator<mixed>
     */
    public function getTagged(string $tag): \Generator;
}
