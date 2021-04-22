<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Select;

use Cycle\ORM\Select;

/**
 * @internal
 * @template V of object
 * @implements \Iterator<array-key,V>
 */
final class LazySelectIterator implements \Iterator, \Countable
{
    /**
     * @var Select<V>|null
     */
    private ?Select $select;

    /**
     * @var \ArrayIterator<array-key,V>|null
     */
    private ?\ArrayIterator $iterator = null;

    /**
     * @param Select<V> $select
     */
    public function __construct(Select $select)
    {
        $this->select = $select;
    }

    public function current()
    {
        return $this->getIterator()->current();
    }

    public function next(): void
    {
        $this->getIterator()->next();
    }

    public function key()
    {
        return $this->getIterator()->key();
    }

    public function valid(): bool
    {
        return $this->getIterator()->valid();
    }

    public function rewind(): void
    {
        $this->getIterator()->rewind();
    }

    public function count(): int
    {
        return $this->getIterator()->count();
    }

    /**
     * @return \ArrayIterator<array-key,V>
     */
    private function getIterator(): \ArrayIterator
    {
        if (null === $this->iterator) {
            \assert(null !== $this->select);
            $this->iterator = new \ArrayIterator(\iterator_to_array($this->select->getIterator(), false));
            $this->select = null;
        }

        return $this->iterator;
    }
}
