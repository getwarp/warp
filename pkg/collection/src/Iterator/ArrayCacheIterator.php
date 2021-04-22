<?php

declare(strict_types=1);

namespace spaceonfire\Collection\Iterator;

/**
 * @template K of array-key
 * @template V
 * @implements \IteratorAggregate<K,V>
 */
final class ArrayCacheIterator implements \IteratorAggregate, \Countable
{
    /**
     * @var \Iterator<K,V>|null
     */
    private ?\Iterator $iterator;

    /**
     * @var array<K,V>
     */
    private array $array = [];

    /**
     * @param \Iterator<K,V> $iterator
     */
    private function __construct(\Iterator $iterator)
    {
        $this->iterator = $iterator;

        if ($iterator instanceof \ArrayIterator) {
            $this->array = $iterator->getArrayCopy();
            $this->iterator = null;
        }
    }

    public function __destruct()
    {
        $this->iterator = null;
        $this->array = [];
    }

    /**
     * @param \Traversable<K,V> $iterator
     * @return self<K,V>
     */
    public static function wrap(\Traversable $iterator): self
    {
        if ($iterator instanceof self) {
            return $iterator;
        }

        while ($iterator instanceof \IteratorAggregate) {
            $iterator = $iterator->getIterator();
        }

        \assert($iterator instanceof \Iterator);

        return new self($iterator);
    }

    /**
     * @return \Generator<K,V>
     */
    public function getIterator(): \Generator
    {
        yield from $this->array;

        if (null === $this->iterator) {
            return;
        }

        while ($this->iterator->valid()) {
            $offset = $this->iterator->key();
            $value = $this->iterator->current();

            yield $offset => $value;
            $this->array[$offset] = $value;

            $this->iterator->next();
        }

        $this->iterator = null;
    }

    public function count(): int
    {
        if (null !== $this->array && null === $this->iterator) {
            return \count($this->array);
        }

        return \iterator_count($this->getIterator());
    }
}
