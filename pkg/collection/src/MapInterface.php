<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

/**
 * Map interface.
 *
 * @template K of array-key
 * @template V
 * @extends \IteratorAggregate<K,V>
 */
interface MapInterface extends \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * @param K $key
     * @param V $element
     */
    public function set($key, $element): void;

    /**
     * @param K $key
     */
    public function unset($key): void;

    /**
     * @param K $key
     * @return bool
     */
    public function has($key): bool;

    /**
     * @param K $key
     * @return V|null
     */
    public function get($key);

    /**
     * @template T
     * @param OperationInterface<K,V,K,T> $operation
     * @return static<K,T>
     */
    public function applyOperation(OperationInterface $operation): self;

    /**
     * @param iterable<K,V> $other
     * @param iterable<K,V> ...$others
     * @return static<K,V>
     */
    public function merge(iterable $other, iterable ...$others): self;

    /**
     * @return CollectionInterface<V>
     */
    public function values(): CollectionInterface;

    /**
     * @return CollectionInterface<K>
     */
    public function keys(): CollectionInterface;

    /**
     * @return K|null
     */
    public function firstKey();

    /**
     * @return K|null
     */
    public function lastKey();

    /**
     * @return \Traversable<K,V>
     */
    public function getIterator(): \Traversable;

    /**
     * @return array<K,V>
     */
    public function jsonSerialize(): array;
}
