<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Collection\Warp;

use Cycle\ORM\Select\ScopeInterface;
use Warp\Bridge\Cycle\Collection\Change;
use Warp\Bridge\Cycle\Collection\ChangesAggregate;
use Warp\Bridge\Cycle\Collection\ChangesEnabledInterface;
use Warp\Bridge\Cycle\Collection\ChangesPlayer;
use Warp\Bridge\Cycle\Collection\ObjectCollectionInterface;
use Warp\Bridge\Cycle\Collection\ObjectCollectionPromiseInterface;
use Warp\Bridge\Cycle\Select\CriteriaScope;
use Warp\Collection\AbstractCollectionDecorator;
use Warp\Collection\CollectionInterface;
use Warp\Common\Factory\StaticConstructorInterface;
use Warp\Criteria\CriteriaInterface;
use Warp\Type\TypeInterface;

/**
 * @template V of object
 * @template P
 * @implements ObjectCollectionPromiseInterface<V,P>
 * @implements ChangesEnabledInterface<V,P>
 * @extends AbstractCollectionDecorator<V>
 */
final class WarpObjectCollectionPromise extends AbstractCollectionDecorator implements ObjectCollectionPromiseInterface, StaticConstructorInterface, ChangesEnabledInterface
{
    /**
     * @var ObjectCollectionPromiseInterface<V,P>
     */
    private ObjectCollectionPromiseInterface $promise;

    private ?TypeInterface $valueType;

    /**
     * @var WarpObjectCollection<V,P>|null
     */
    private ?WarpObjectCollection $collection;

    /**
     * @var ChangesAggregate<V,P>
     */
    private ChangesAggregate $changes;

    /**
     * @param ObjectCollectionPromiseInterface<V,P> $promise
     * @param TypeInterface|null $valueType
     * @param WarpObjectCollection<V,P>|null $collection
     * @param ChangesAggregate<V,P>|null $changes
     */
    private function __construct(
        ObjectCollectionPromiseInterface $promise,
        ?TypeInterface $valueType = null,
        ?WarpObjectCollection $collection = null,
        ?ChangesAggregate $changes = null
    ) {
        $this->promise = $promise;
        $this->valueType = $valueType;
        $this->collection = $collection;
        // @phpstan-ignore-next-line
        $this->changes = $changes ?? ChangesAggregate::new();
    }

    public function __id(): int
    {
        return $this->promise->__id();
    }

    public function __role(): string
    {
        return $this->promise->__role();
    }

    public function __scope(): array
    {
        return $this->promise->__scope();
    }

    public function __loaded(): bool
    {
        return $this->promise->__loaded();
    }

    public function __resolve(): ObjectCollectionInterface
    {
        return $this->getCollection();
    }

    /**
     * @param ObjectCollectionPromiseInterface<V,P> $promise
     * @param TypeInterface|null $valueType
     * @return self<V,P>
     */
    public static function new(ObjectCollectionPromiseInterface $promise, ?TypeInterface $valueType = null): self
    {
        return new self($promise, $valueType);
    }

    public function getScope(): ScopeInterface
    {
        return $this->promise->getScope();
    }

    /**
     * @param ScopeInterface $scope
     * @return self<V,P>
     */
    public function withScope(ScopeInterface $scope): self
    {
        $scoped = $this->promise->withScope($scope);
        return $scoped === $this->promise ? $this : new self($scoped, $this->valueType, null, clone $this->changes);
    }

    public function hasPivot(object $element): bool
    {
        if (null !== $change = $this->changes->get($element)) {
            return null !== $change->getPivot();
        }

        return $this->__resolve()->hasPivot($element);
    }

    public function getPivot(object $element)
    {
        if (null !== $change = $this->changes->get($element)) {
            return $change->getPivot();
        }

        return $this->__resolve()->getPivot($element);
    }

    public function setPivot(object $element, $pivot): void
    {
        if (null !== $change = $this->changes->get($element)) {
            $change->setPivot($pivot);
            return;
        }

        $this->__resolve()->setPivot($element, $pivot);
    }

    public function getPivotContext(): \SplObjectStorage
    {
        return $this->__resolve()->getPivotContext();
    }

    public function matching(CriteriaInterface $criteria): CollectionInterface
    {
        if ($this->__loaded() || $this->changes->hasChanges()) {
            return parent::matching($criteria);
        }

        return $this->withScope(new CriteriaScope($criteria));
    }

    public function count(): int
    {
        if ($this->__loaded() || $this->changes->hasChanges()) {
            return parent::count();
        }

        return $this->promise->count();
    }

    public function add($element, ...$elements): void
    {
        if ($this->__loaded()) {
            parent::add($element, ...$elements);

            return;
        }

        // @phpstan-ignore-next-line
        $this->recordChanges(...Change::addElements($element, ...$elements));
    }

    public function remove($element, ...$elements): void
    {
        if ($this->__loaded()) {
            parent::remove($element, ...$elements);

            return;
        }

        // @phpstan-ignore-next-line
        $this->recordChanges(...Change::removeElements($element, ...$elements));
    }

    public function replace($element, $replacement): void
    {
        if ($this->__loaded()) {
            parent::replace($element, $replacement);

            return;
        }

        // @phpstan-ignore-next-line
        $this->recordChanges(Change::remove($element), Change::add($replacement));
    }

    public function hasChanges(): bool
    {
        return $this->changes->hasChanges();
    }

    public function releaseChanges(): array
    {
        $changes = \iterator_to_array($this->changes, false);
        $this->changes->clean();
        return $changes;
    }

    /**
     * @param Change<V,P> $change
     * @param Change<V,P> ...$changes
     */
    protected function recordChanges(Change $change, Change ...$changes): void
    {
        $this->changes->recordChanges($change, ...$changes);
    }

    /**
     * @return WarpObjectCollection<V,P>
     */
    protected function getCollection(): WarpObjectCollection
    {
        if (null === $this->collection) {
            $this->collection = WarpObjectCollection::new(
                (new ChangesPlayer())->play($this->promise->__resolve()->getPivotContext(), $this->releaseChanges()),
                $this->valueType,
            );
        }

        return $this->collection;
    }
}
