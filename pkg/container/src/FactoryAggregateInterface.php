<?php

declare(strict_types=1);

namespace spaceonfire\Container;

interface FactoryAggregateInterface
{
    /**
     * @template T
     * @param class-string<T> $class
     * @return bool
     */
    public function hasFactory(string $class): bool;

    /**
     * @template T
     * @param class-string<T> $class
     * @return FactoryInterface<T>
     */
    public function getFactory(string $class): FactoryInterface;

    /**
     * Create instance of requested definition with given arguments.
     * @template T
     * @param class-string<T> $class
     * @param FactoryOptionsInterface|array<string,mixed>|null $options
     * @return T
     */
    public function make(string $class, $options = null);
}
