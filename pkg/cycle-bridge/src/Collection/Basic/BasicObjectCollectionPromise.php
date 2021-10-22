<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Basic;

use Cycle\ORM\Select\ScopeInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionPromiseInterface;
use spaceonfire\Bridge\Cycle\Collection\Relation\ToManyRelationInterface;
use spaceonfire\Bridge\Cycle\Select\ReferenceScope;
use spaceonfire\Bridge\Cycle\Select\ScopeAggregate;

/**
 * @template T of object
 * @template P
 * @implements ObjectCollectionPromiseInterface<T,P>
 * @implements \IteratorAggregate<array-key,T>
 */
final class BasicObjectCollectionPromise implements ObjectCollectionPromiseInterface, \IteratorAggregate
{
    private ToManyRelationInterface $relation;

    private ReferenceScope $promiseScope;

    private ScopeInterface $scope;

    /**
     * @var ObjectCollectionInterface<T,P>|null
     */
    private ?ObjectCollectionInterface $collection = null;

    public function __construct(ToManyRelationInterface $relation, ReferenceScope $scope)
    {
        $this->relation = $relation;
        $this->promiseScope = $scope;
        $this->scope = $scope;
    }

    public function __id(): int
    {
        return \spl_object_id($this);
    }

    public function __role(): string
    {
        return $this->relation->getTarget();
    }

    public function __scope(): array
    {
        return $this->promiseScope->getScope();
    }

    public function __loaded(): bool
    {
        return null !== $this->collection;
    }

    public function __resolve(): ObjectCollectionInterface
    {
        if (null === $this->collection) {
            /** @phpstan-var ObjectCollectionInterface<T,P> $collection */
            $collection = $this->relation->loadCollection($this);
            $this->collection = $collection;
        }

        return $this->collection;
    }

    public function getScope(): ScopeInterface
    {
        if ($this->promiseScope === $this->scope) {
            return $this->scope;
        }

        return new ScopeAggregate($this->promiseScope, $this->scope);
    }

    /**
     * @param ScopeInterface $scope
     * @return self<T,P>
     */
    public function withScope(ScopeInterface $scope): self
    {
        if ($scope === $this->scope) {
            return $this;
        }

        /** @phpstan-var self<T,P> $clone */
        $clone = new self($this->relation, $this->promiseScope);
        $clone->scope = $scope;
        return $clone;
    }

    public function hasPivot(object $element): bool
    {
        return $this->__resolve()->hasPivot($element);
    }

    public function getPivot(object $element)
    {
        return $this->__resolve()->getPivot($element);
    }

    public function setPivot(object $element, $pivot): void
    {
        $this->__resolve()->setPivot($element, $pivot);
    }

    public function getPivotContext(): \SplObjectStorage
    {
        return $this->__resolve()->getPivotContext();
    }

    /**
     * @return \Generator<T>
     */
    public function getIterator(): \Generator
    {
        return yield from $this->__resolve();
    }

    public function count(): int
    {
        if (null !== $this->collection) {
            return $this->collection instanceof \Countable
                ? \count($this->collection)
                : \iterator_count($this->collection);
        }

        return $this->relation->countCollection($this);
    }
}
