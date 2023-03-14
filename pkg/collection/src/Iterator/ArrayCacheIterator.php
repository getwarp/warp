<?php

declare(strict_types=1);

namespace Warp\Collection\Iterator;

/**
 * @template K of array-key
 * @template V
 * @implements \Iterator<K,V>
 */
final class ArrayCacheIterator implements \Iterator, \Countable
{
    /**
     * @var \Iterator<K,V>|null
     */
    private ?\Iterator $iterator;

    /**
     * @var array<int,K>
     */
    private array $keys = [];

    /**
     * @var array<int,V>
     */
    private array $values = [];

    private int $pos = 0;

    /**
     * @param \Iterator<K,V> $iterator
     */
    private function __construct(\Iterator $iterator)
    {
        $this->iterator = $iterator;

        if ($iterator instanceof \ArrayIterator) {
            $array = $iterator->getArrayCopy();
            // @phpstan-ignore-next-line
            $this->keys = \array_keys($array);
            $this->values = \array_values($array);
            $this->iterator = null;
        }
    }

    public function __destruct()
    {
        $this->iterator = null;
        $this->keys = [];
        $this->values = [];
        $this->pos = 0;
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

    public function rewind(): void
    {
        $this->pos = 0;
        $this->cacheTuple($this->pos);
    }

    public function valid(): bool
    {
        return isset($this->keys[$this->pos]) || (null !== $this->iterator && $this->iterator->valid());
    }

    /**
     * @return V
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        $this->cacheTuple($this->pos);
        return $this->values[$this->pos];
    }

    /**
     * @return K
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        $this->cacheTuple($this->pos);
        return $this->keys[$this->pos];
    }

    public function next(): void
    {
        ++$this->pos;

        if (null === $this->iterator) {
            return;
        }

        if (isset($this->keys[$this->pos])) {
            return;
        }

        $this->iterator->next();

        if ($this->iterator->valid()) {
            $this->cacheTuple($this->pos);
        } else {
            $this->iterator = null;
        }
    }

    public function count(): int
    {
        if (null === $this->iterator) {
            return \count($this->keys);
        }

        $pos = $this->pos;
        $count = \iterator_count($this);
        $this->pos = $pos;
        return $count;
    }

    /**
     * @return array<K,V>
     */
    public function getArrayCopy(): array
    {
        $pos = $this->pos;
        $array = \iterator_to_array($this);
        $this->pos = $pos;
        return $array;
    }

    private function cacheTuple(int $pos): void
    {
        if (isset($this->keys[$pos])) {
            return;
        }

        if (null === $this->iterator || !$this->iterator->valid()) {
            return;
        }

        $this->keys[$pos] = $this->iterator->key();
        $this->values[$pos] = $this->iterator->current();
    }
}
