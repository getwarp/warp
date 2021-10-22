<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Onfire;

use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectStorage;
use spaceonfire\Collection\MutableInterface;

/**
 * @template V of object
 * @template P
 * @implements ObjectCollectionInterface<V,P>
 * @implements MutableInterface<V>
 * @implements \IteratorAggregate<array-key,V>
 */
final class ObjectIterator implements ObjectCollectionInterface, MutableInterface, \IteratorAggregate, \Countable
{
    /**
     * @var ObjectStorage<V,P|null>
     */
    private ObjectStorage $storage;

    /**
     * @param V[] $elements
     */
    public function __construct(iterable $elements = [])
    {
        // @phpstan-ignore-next-line
        $this->storage = $elements instanceof ObjectStorage ? $elements : ObjectStorage::snapshot($elements);
    }

    public function clear(): void
    {
        // @phpstan-ignore-next-line
        $this->storage = new ObjectStorage();
    }

    public function add($element, ...$elements): void
    {
        foreach ([$element, ...$elements] as $e) {
            $this->storage->attach($e);
        }
    }

    public function remove($element, ...$elements): void
    {
        foreach ([$element, ...$elements] as $e) {
            $this->storage->detach($e);
        }
    }

    public function replace($element, $replacement): void
    {
        if ($element === $replacement) {
            return;
        }

        $this->storage->attach($replacement, $this->storage->getPivot($element));
        $this->storage->detach($element);
    }

    public function hasPivot(object $element): bool
    {
        return $this->storage->hasPivot($element);
    }

    public function getPivot(object $element)
    {
        return $this->storage->getPivot($element);
    }

    public function setPivot(object $element, $pivot): void
    {
        $this->storage->setPivot($element, $pivot);
    }

    public function getPivotContext(): \SplObjectStorage
    {
        return $this->storage->getPivotContext();
    }

    /**
     * @return \Generator<V>
     */
    public function getIterator(): \Generator
    {
        return yield from $this->storage;
    }

    public function count(): int
    {
        return $this->storage->count();
    }
}
