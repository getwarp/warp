<?php

declare(strict_types=1);

namespace spaceonfire\Collection\Iterator;

use spaceonfire\Collection\MutableInterface;

/**
 * @template K of array-key
 * @template V
 * @implements \IteratorAggregate<K,V>
 * @implements \ArrayAccess<K,V>
 * @implements MutableInterface<V>
 */
final class ArrayIterator implements \IteratorAggregate, \ArrayAccess, \Countable, MutableInterface
{
    /**
     * @var \ArrayIterator<K,V>
     */
    private \ArrayIterator $iterator;

    /**
     * @param iterable<K,V> $items
     */
    public function __construct(iterable $items = [])
    {
        $this->iterator = $this->prepareIterator($items);
    }

    public function clear(): void
    {
        $this->iterator = new \ArrayIterator([], $this->iterator->getFlags());
    }

    public function add($element, ...$elements): void
    {
        foreach ([$element, ...$elements] as $e) {
            $this->iterator[] = $e;
        }
    }

    public function remove($element, ...$elements): void
    {
        $elements = [$element, ...$elements];

        $this->iterator = new \ArrayIterator(
            \array_filter(
                $this->iterator->getArrayCopy(),
                static fn ($element) => !\in_array($element, $elements, true)
            ),
            $this->iterator->getFlags()
        );
    }

    public function replace($element, $replacement): void
    {
        $array = $this->iterator->getArrayCopy();
        $offset = \array_search($element, $array, true);

        if (false === $offset) {
            return;
        }

        $array[$offset] = $replacement;

        $this->iterator = new \ArrayIterator($array, $this->iterator->getFlags());
    }

    /**
     * @param K $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->iterator->offsetExists($offset);
    }

    /**
     * @param K $offset
     * @return V|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->iterator->offsetGet($offset);
    }

    /**
     * @param K $offset
     * @param V $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->iterator->offsetSet($offset, $value);
    }

    /**
     * @param K $offset
     */
    public function offsetUnset($offset): void
    {
        $this->iterator->offsetUnset($offset);
    }

    /**
     * @return array<K,V>
     */
    public function getArrayCopy(): array
    {
        return $this->iterator->getArrayCopy();
    }

    /**
     * @return \Generator<K,V>
     */
    public function getIterator(): \Generator
    {
        return yield from $this->getArrayCopy();
    }

    public function count(): int
    {
        return $this->iterator->count();
    }

    /**
     * @param iterable<K,V> $iterator
     * @return \ArrayIterator<int|K,V>
     */
    private function prepareIterator(iterable $iterator): \ArrayIterator
    {
        if ($iterator instanceof \ArrayIterator) {
            return $iterator;
        }

        return new \ArrayIterator($iterator instanceof \Traversable ? \iterator_to_array($iterator) : $iterator);
    }
}
