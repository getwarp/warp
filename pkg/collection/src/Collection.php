<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use spaceonfire\Collection\Iterator\ArrayCacheIterator;
use spaceonfire\Collection\Iterator\ArrayIterator;
use spaceonfire\Common\Factory\StaticConstructorInterface;
use spaceonfire\Type\MixedType;
use spaceonfire\Type\TypeInterface;

/**
 * @template V
 * @extends AbstractCollection<V>
 */
final class Collection extends AbstractCollection implements StaticConstructorInterface
{
    /**
     * @param iterable<V> $elements
     * @param TypeInterface|null $valueType
     * @return self<V>
     */
    public static function new(iterable $elements = [], ?TypeInterface $valueType = null): self
    {
        if ($elements instanceof self) {
            // TODO: check that type of $elements equals to given value type
            return $elements;
        }

        $valueType ??= MixedType::new();

        $iterator = $elements;

        if (\is_array($iterator)) {
            $iterator = new \ArrayIterator($iterator);
        }

        if ($iterator instanceof \Generator) {
            $iterator = ArrayCacheIterator::wrap($iterator);
        }

        return new self($iterator, $valueType);
    }

    protected function withSource(\Traversable $source, ?TypeInterface $valueType = null): AbstractCollection
    {
        // We need to prepare iterator of current collection, in case it will be used later
        $this->prepareIterator();

        return new self($source, $valueType ?? $this->valueType);
    }

    protected function getMutableSource(): MutableInterface
    {
        return $this->source instanceof MutableInterface
            ? $this->source
            : new ArrayIterator(\iterator_to_array($this->source, false));
    }

    protected function makeMap(iterable $elements = [], ?TypeInterface $valueType = null): MapInterface
    {
        return Map::new($elements, $valueType);
    }
}
