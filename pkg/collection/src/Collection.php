<?php

declare(strict_types=1);

namespace Warp\Collection;

use Warp\Collection\Iterator\ArrayCacheIterator;
use Warp\Collection\Iterator\ArrayIterator;
use Warp\Common\Factory\StaticConstructorInterface;
use Warp\Type\MixedType;
use Warp\Type\TypeInterface;

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
        return new self($source, $valueType ?? $this->valueType);
    }

    protected function getMutableSource(): MutableInterface
    {
        return $this->source instanceof MutableInterface
            ? $this->source
            : new ArrayIterator($this->prepareIterator()->getArrayCopy());
    }

    protected function makeMap(iterable $elements = [], ?TypeInterface $valueType = null): MapInterface
    {
        return Map::new($elements, $valueType);
    }
}
