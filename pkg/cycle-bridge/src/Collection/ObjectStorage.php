<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection;

/**
 * @template V of object
 * @template P
 * @implements ObjectCollectionInterface<V,P>
 * @implements \IteratorAggregate<array-key,V>
 */
final class ObjectStorage implements ObjectCollectionInterface, \IteratorAggregate, \Countable
{
    /**
     * @var \SplObjectStorage<V,P|null>
     */
    private \SplObjectStorage $storage;

    /**
     * @param \SplObjectStorage<V,P|null>|null $storage
     */
    public function __construct(?\SplObjectStorage $storage = null)
    {
        $this->storage = new \SplObjectStorage();

        if (null !== $storage) {
            $this->storage->addAll($storage);
        }
    }

    public function hasPivot(object $element): bool
    {
        return null !== $this->getPivot($element);
    }

    public function getPivot(object $element)
    {
        return $this->storage[$element] ?? null;
    }

    public function setPivot(object $element, $pivot): void
    {
        if (!$this->storage->offsetExists($element)) {
            return;
        }

        $this->storage->attach($element, $pivot);
    }

    public function getPivotContext(): \SplObjectStorage
    {
        return $this->storage;
    }

    /**
     * @param V $element
     * @param P|null $pivot
     */
    public function attach(object $element, $pivot = null): void
    {
        $this->storage->attach($element, $pivot ?? $this->storage[$element] ?? null);
    }

    /**
     * @param V $element
     */
    public function detach(object $element): void
    {
        $this->storage->detach($element);
    }

    /**
     * @return \Traversable<int,V>
     */
    public function getIterator(): \Traversable
    {
        // @phpstan-ignore-next-line
        return \SplFixedArray::fromArray(\iterator_to_array($this->storage, false));
    }

    public function count(): int
    {
        return $this->storage->count();
    }

    /**
     * @param iterable<object>|null $iterator
     * @return self<object,mixed>
     */
    public static function snapshot(?iterable $iterator = null): self
    {
        if (null === $iterator) {
            return new self();
        }

        if ($iterator instanceof ObjectCollectionInterface) {
            return new self($iterator->getPivotContext());
        }

        if ($iterator instanceof \SplObjectStorage) {
            return new self($iterator);
        }

        $storage = new \SplObjectStorage();
        foreach ($iterator as $element) {
            $storage->attach($element);
        }

        return new self($storage);
    }
}
