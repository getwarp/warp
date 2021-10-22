<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use spaceonfire\Common\Field\FieldInterface;
use spaceonfire\Criteria\CriteriaInterface;

/**
 * @template V
 * @implements CollectionInterface<V>
 */
abstract class AbstractCollectionDecorator implements CollectionInterface
{
    public function applyOperation(OperationInterface $operation): CollectionInterface
    {
        return $this->getCollection()->applyOperation($operation);
    }

    public function filter(?callable $callback = null): CollectionInterface
    {
        return $this->getCollection()->filter($callback);
    }

    public function map(callable $callback): CollectionInterface
    {
        return $this->getCollection()->map($callback);
    }

    public function reverse(): CollectionInterface
    {
        return $this->getCollection()->reverse();
    }

    public function merge(iterable $other, iterable ...$others): CollectionInterface
    {
        return $this->getCollection()->merge($other, ...$others);
    }

    public function unique(): CollectionInterface
    {
        return $this->getCollection()->unique();
    }

    public function sort(?FieldInterface $field = null, int $direction = \SORT_ASC): CollectionInterface
    {
        return $this->getCollection()->sort($field, $direction);
    }

    public function slice(int $offset, ?int $limit = null): CollectionInterface
    {
        return $this->getCollection()->slice($offset, $limit);
    }

    public function matching(CriteriaInterface $criteria): CollectionInterface
    {
        return $this->getCollection()->matching($criteria);
    }

    public function all(): array
    {
        return $this->getCollection()->all();
    }

    public function find(callable $callback)
    {
        return $this->getCollection()->find($callback);
    }

    public function contains($element): bool
    {
        return $this->getCollection()->contains($element);
    }

    public function first()
    {
        return $this->getCollection()->first();
    }

    public function last()
    {
        return $this->getCollection()->last();
    }

    public function reduce(callable $callback, $initialValue = null)
    {
        return $this->getCollection()->reduce($callback, $initialValue);
    }

    public function implode(?string $glue = null, ?FieldInterface $field = null): string
    {
        return $this->getCollection()->implode($glue, $field);
    }

    public function sum(?FieldInterface $field = null)
    {
        return $this->getCollection()->sum($field);
    }

    public function average(?FieldInterface $field = null)
    {
        return $this->getCollection()->average($field);
    }

    public function median(?FieldInterface $field = null)
    {
        return $this->getCollection()->median($field);
    }

    public function max(?FieldInterface $field = null)
    {
        return $this->getCollection()->max($field);
    }

    public function min(?FieldInterface $field = null)
    {
        return $this->getCollection()->max($field);
    }

    public function indexBy($keyExtractor): MapInterface
    {
        return $this->getCollection()->indexBy($keyExtractor);
    }

    public function groupBy($keyExtractor): MapInterface
    {
        return $this->getCollection()->groupBy($keyExtractor);
    }

    public function getIterator(): \Traversable
    {
        return $this->getCollection();
    }

    public function count(): int
    {
        return $this->getCollection()->count();
    }

    public function clear(): void
    {
        $this->getCollection()->clear();
    }

    public function add($element, ...$elements): void
    {
        $this->getCollection()->add($element, ...$elements);
    }

    public function remove($element, ...$elements): void
    {
        $this->getCollection()->remove($element, ...$elements);
    }

    public function replace($element, $replacement): void
    {
        $this->getCollection()->replace($element, $replacement);
    }

    /**
     * @return CollectionInterface<V>
     */
    public function jsonSerialize(): CollectionInterface
    {
        return $this->getCollection();
    }

    /**
     * @return CollectionInterface<V>
     */
    abstract protected function getCollection(): CollectionInterface;
}
