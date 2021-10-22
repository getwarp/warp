<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Basic;

use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectStorage;

/**
 * @template T of object
 * @template P
 * @implements ObjectCollectionInterface<T,P>
 * @implements \IteratorAggregate<array-key,T>
 */
final class BasicObjectCollection implements ObjectCollectionInterface, \IteratorAggregate, \Countable
{
    /**
     * @var ObjectStorage<T,P|null>
     */
    private ObjectStorage $storage;

    /**
     * @param iterable<T> $elements
     */
    public function __construct(iterable $elements = [])
    {
        // @phpstan-ignore-next-line
        $this->storage = $elements instanceof ObjectStorage ? $elements : ObjectStorage::snapshot($elements);
    }

    /**
     * @return \Generator<T>
     */
    public function getIterator(): \Generator
    {
        return yield from $this->storage;
    }

    public function count(): int
    {
        return $this->storage->count();
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
}
