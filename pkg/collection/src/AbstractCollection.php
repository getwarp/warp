<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use spaceonfire\Collection\Iterator\ArrayCacheIterator;
use spaceonfire\Common\Field\FieldFactoryInterface;
use spaceonfire\Common\Field\FieldInterface;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\Criteria\FilterableInterface;
use spaceonfire\Type\MixedType;
use spaceonfire\Type\TypeInterface;

/**
 * @template V
 * @implements CollectionInterface<V>
 */
abstract class AbstractCollection implements CollectionInterface, FilterableInterface
{
    /**
     * @var \Traversable<int,V>
     */
    protected \Traversable $source;

    protected TypeInterface $valueType;

    private bool $sourcePrepared = false;

    private ?FieldFactoryInterface $fieldFactory = null;

    /**
     * @param \Traversable<int,V> $source
     * @param TypeInterface $valueType
     */
    protected function __construct(\Traversable $source, TypeInterface $valueType)
    {
        $this->source = $source;
        $this->valueType = $valueType;
    }

    public function clear(): void
    {
        $this->source = $this->getMutableSource();
        $this->source->clear();
    }

    public function add($element, ...$elements): void
    {
        $this->assertElementsType($element, ...$elements);
        $this->source = $this->getMutableSource();
        $this->source->add($element, ...$elements);
    }

    public function remove($element, ...$elements): void
    {
        $this->assertElementsType($element, ...$elements);
        $this->source = $this->getMutableSource();
        $this->source->remove($element, ...$elements);
    }

    public function replace($element, $replacement): void
    {
        $this->assertElementsType($element, $replacement);
        $this->source = $this->getMutableSource();
        $this->source->replace($element, $replacement);
    }

    public function applyOperation(OperationInterface $operation): CollectionInterface
    {
        return $this->withSource(
            $operation->apply($this->source),
            $operation instanceof AlterValueTypeOperationInterface ? MixedType::new() : null
        );
    }

    public function filter(?callable $callback = null): CollectionInterface
    {
        return $this->applyOperation(new Operation\FilterOperation($callback));
    }

    public function map(callable $callback): CollectionInterface
    {
        return $this->applyOperation(new Operation\MapOperation($callback));
    }

    public function reverse(): CollectionInterface
    {
        return $this->applyOperation(new Operation\ReverseOperation());
    }

    public function merge(iterable $other, iterable ...$others): CollectionInterface
    {
        return $this->applyOperation(new Operation\MergeOperation([$other, ...$others]));
    }

    public function unique(): CollectionInterface
    {
        return $this->applyOperation(new Operation\UniqueOperation());
    }

    public function sort(?FieldInterface $field = null, int $direction = \SORT_ASC): CollectionInterface
    {
        return $this->applyOperation(new Operation\SortOperation($direction, $field));
    }

    public function slice(int $offset, ?int $limit = null): CollectionInterface
    {
        return $this->applyOperation(new Operation\SliceOperation($offset, $limit));
    }

    /**
     * @param CriteriaInterface $criteria
     * @return CollectionInterface<V>
     */
    public function matching(CriteriaInterface $criteria): CollectionInterface
    {
        return $this->applyOperation(new Operation\MatchingOperation($criteria, $this->fieldFactory));
    }

    public function all(): array
    {
        return \iterator_to_array($this->getIterator(), false);
    }

    public function find(callable $callback)
    {
        $iter = (new Operation\FirstOperation($callback))->apply($this->getIterator());

        return $iter->valid()
            ? $iter->current()
            : null;
    }

    public function contains($element): bool
    {
        return null !== $this->find(static fn ($v) => $element === $v);
    }

    public function first()
    {
        $iter = (new Operation\FirstOperation())->apply($this->getIterator());

        return $iter->valid()
            ? $iter->current()
            : null;
    }

    public function last()
    {
        $iter = (new Operation\LastOperation())->apply($this->getIterator());

        return $iter->valid()
            ? $iter->current()
            : null;
    }

    public function reduce(callable $callback, $initialValue = null)
    {
        $iter = (new Operation\ReduceOperation($callback, $initialValue))->apply($this->getIterator());

        return $iter->valid()
            ? $iter->current()
            : $initialValue;
    }

    public function implode(?string $glue = null, ?FieldInterface $field = null): string
    {
        $iter = (new Operation\ImplodeOperation($glue, $field))->apply($this->getIterator());

        return $iter->valid()
            ? $iter->current()
            : '';
    }

    public function sum(?FieldInterface $field = null)
    {
        $iter = (new Operation\SumOperation($field))->apply($this->getIterator());

        return $iter->valid()
            ? $iter->current()
            : 0;
    }

    public function average(?FieldInterface $field = null)
    {
        $iter = (new Operation\AverageOperation($field))->apply($this->getIterator());

        return $iter->valid()
            ? $iter->current()
            : null;
    }

    public function median(?FieldInterface $field = null)
    {
        $iter = (new Operation\MedianOperation($field))->apply($this->getIterator());

        return $iter->valid()
            ? $iter->current()
            : null;
    }

    public function max(?FieldInterface $field = null)
    {
        $iter = (new Operation\MaximumOperation($field))->apply($this->getIterator());

        return $iter->valid()
            ? $iter->current()
            : null;
    }

    public function min(?FieldInterface $field = null)
    {
        $iter = (new Operation\MinimumOperation($field))->apply($this->getIterator());

        return $iter->valid()
            ? $iter->current()
            : null;
    }

    public function indexBy($keyExtractor): MapInterface
    {
        return $this->makeMap(
            (new Operation\IndexByOperation($keyExtractor))->apply($this->getIterator()),
            $this->valueType,
        );
    }

    public function groupBy($keyExtractor): MapInterface
    {
        /** @var array<array-key,CollectionInterface<V>> $map */
        $map = [];

        $iter = (new Operation\IndexByOperation($keyExtractor))->apply($this->getIterator());

        foreach ($iter as $key => $element) {
            $map[$key] ??= $this->withSource(new \ArrayIterator());
            $map[$key]->add($element);
        }

        return $this->makeMap($map);
    }

    /**
     * @return \Traversable<int,V>
     */
    public function getIterator(): \Traversable
    {
        return $this->prepareIterator();
    }

    public function count(): int
    {
        if (!$this->source instanceof \Countable) {
            $this->source = $this->prepareIterator();
        }

        return $this->source->count();
    }

    /**
     * @inheritDoc
     * @phpstan-return list<V>
     */
    public function jsonSerialize(): array
    {
        return $this->all();
    }

    /**
     * @template T
     * @param \Traversable<int,T> $source
     * @param TypeInterface|null $valueType
     * @return static<T>
     */
    abstract protected function withSource(\Traversable $source, ?TypeInterface $valueType = null): CollectionInterface;

    /**
     * @return \Traversable|MutableInterface
     * @phpstan-return MutableInterface<V>&\Traversable<int,V>
     */
    abstract protected function getMutableSource(): MutableInterface;

    /**
     * @template MapK of array-key
     * @template MapV
     * @param iterable<MapK,MapV> $elements
     * @param TypeInterface|null $valueType
     * @return MapInterface<MapK,MapV>
     */
    abstract protected function makeMap(iterable $elements = [], ?TypeInterface $valueType = null): MapInterface;

    /**
     * @param V ...$elements
     */
    protected function assertElementsType(...$elements): void
    {
        foreach ($elements as $element) {
            if ($this->valueType->check($element)) {
                continue;
            }

            throw new \LogicException(\sprintf(
                'Collection accepts only elements of type %s. Got: %s.',
                $this->valueType,
                \get_debug_type($element)
            ));
        }
    }

    /**
     * @return ArrayCacheIterator<int,V>
     */
    private function prepareIterator(): ArrayCacheIterator
    {
        $this->sourcePrepared = $this->sourcePrepared && $this->source instanceof ArrayCacheIterator;

        if ($this->sourcePrepared) {
            \assert($this->source instanceof ArrayCacheIterator);
            return $this->source;
        }

        $iterator = $this->source;
        $iterator = (new Operation\ValuesOperation())->apply($iterator);
        $iterator = (new Operation\TypeCheckOperation($this->valueType))->apply($iterator);

        $this->source = ArrayCacheIterator::wrap($iterator);
        $this->sourcePrepared = true;

        return $this->source;
    }
}
