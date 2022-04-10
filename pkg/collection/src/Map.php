<?php

declare(strict_types=1);

namespace Warp\Collection;

use Warp\Collection\Iterator\ArrayIterator;
use Warp\Common\Factory\StaticConstructorInterface;
use Warp\Type\MixedType;
use Warp\Type\TypeInterface;

/**
 * @template K of array-key
 * @template V
 * @extends AbstractMap<K,V>
 */
final class Map extends AbstractMap implements StaticConstructorInterface
{
    /**
     * @param iterable<K,V> $elements
     * @param TypeInterface|null $valueType
     * @return self<K,V>
     */
    public static function new(iterable $elements = [], ?TypeInterface $valueType = null): self
    {
        if ($elements instanceof self) {
            return $elements;
        }

        $valueType ??= MixedType::new();

        $iterator = \is_array($elements) ? new \ArrayIterator($elements) : $elements;
        $iterator = (new Operation\TypeCheckOperation($valueType, true))->apply($iterator);

        return new self(new ArrayIterator($iterator), $valueType);
    }

    protected function withSource(ArrayIterator $source, ?TypeInterface $valueType = null): MapInterface
    {
        return new self($source, $valueType ?? $this->valueType);
    }

    protected function makeCollection(iterable $elements = [], ?TypeInterface $valueType = null): CollectionInterface
    {
        return Collection::new($elements, $valueType);
    }
}
