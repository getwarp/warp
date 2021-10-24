<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Onfire;

use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionInterface;
use spaceonfire\Collection\AbstractCollectionDecorator;
use spaceonfire\Collection\Collection;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Common\Factory\StaticConstructorInterface;
use spaceonfire\Type\TypeInterface;

/**
 * @template V of object
 * @template P
 * @extends AbstractCollectionDecorator<V>
 * @implements ObjectCollectionInterface<V,P>
 */
final class OnfireObjectCollection extends AbstractCollectionDecorator implements ObjectCollectionInterface, StaticConstructorInterface
{
    /**
     * @var ObjectIterator<V,P>
     */
    private ObjectIterator $storage;

    /**
     * @var CollectionInterface<V>
     */
    private CollectionInterface $collection;

    /**
     * @param ObjectIterator<V,P> $storage
     * @param CollectionInterface<V> $collection
     */
    private function __construct(ObjectIterator $storage, CollectionInterface $collection)
    {
        $this->storage = $storage;
        $this->collection = $collection;
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
        $collection = Collection::new($iterator, $valueType);
        return new self($iterator, $collection);
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

    protected function getCollection(): CollectionInterface
    {
        return $this->collection;
    }
}
