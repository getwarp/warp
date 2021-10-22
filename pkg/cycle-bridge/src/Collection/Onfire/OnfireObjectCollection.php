<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Onfire;

use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionInterface;
use spaceonfire\Collection\AbstractCollection;
use spaceonfire\Collection\Iterator\ArrayCacheIterator;
use spaceonfire\Collection\Map;
use spaceonfire\Collection\MapInterface;
use spaceonfire\Common\Factory\StaticConstructorInterface;
use spaceonfire\Type\MixedType;
use spaceonfire\Type\TypeInterface;

/**
 * @template V of object
 * @template P
 * @extends AbstractCollection<V>
 * @implements ObjectCollectionInterface<V,P>
 */
final class OnfireObjectCollection extends AbstractCollection implements ObjectCollectionInterface, StaticConstructorInterface
{
    /**
     * @var ObjectIterator<V,P>
     */
    private ObjectIterator $storage;

    /**
     * @param ObjectIterator<V,P> $storage
     * @param \Traversable<int,V> $source
     * @param TypeInterface $valueType
     */
    protected function __construct(ObjectIterator $storage, \Traversable $source, TypeInterface $valueType)
    {
        $this->storage = $storage;

        parent::__construct($source, $valueType);
    }

    /**
     * @template T of object
     * @param iterable<T> $elements
     * @param TypeInterface|null $valueType
     * @return self<T,mixed>
     */
    public static function new(iterable $elements = [], ?TypeInterface $valueType = null): self
    {
        $iterator = new ObjectIterator($elements);

        return new self($iterator, $iterator, $valueType ?? MixedType::new());
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
     * @param \Traversable<array-key,V> $source
     * @param TypeInterface|null $valueType
     * @return self<V,P>
     */
    protected function withSource(\Traversable $source, ?TypeInterface $valueType = null): self
    {
        return new self($this->storage, $source, $valueType ?? $this->valueType);
    }

    /**
     * @return ObjectIterator<V,P>
     */
    protected function getMutableSource(): ObjectIterator
    {
        $this->prepareIterator();
        return $this->storage;
    }

    protected function makeMap(iterable $elements = [], ?TypeInterface $valueType = null): MapInterface
    {
        return Map::new($elements, $valueType);
    }

    protected function prepareIterator(): ArrayCacheIterator
    {
        $source = parent::prepareIterator();

        $this->storage = self::filterStorage($this->storage, $source);

        return $source;
    }

    /**
     * @param ObjectIterator<V,P> $storage
     * @param ArrayCacheIterator<array-key,V> $elements
     * @return ObjectIterator<V,P>
     */
    private static function filterStorage(ObjectIterator $storage, ArrayCacheIterator $elements): ObjectIterator
    {
        /** @phpstan-var ObjectIterator<V,P> $output */
        $output = new ObjectIterator($elements);

        foreach ($elements as $element) {
            $output->setPivot($element, $storage->getPivot($element));
        }

        return $output;
    }
}
