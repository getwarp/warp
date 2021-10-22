<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Doctrine;

use Cycle\ORM\Select\ScopeInterface;
use Doctrine\Common\Collections\AbstractLazyCollection;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionPromiseInterface;

/**
 * @template T of object
 * @template P
 * @implements ObjectCollectionPromiseInterface<T,P>
 * @extends AbstractLazyCollection<array-key,T>
 */
final class DoctrineObjectCollectionPromise extends AbstractLazyCollection implements ObjectCollectionPromiseInterface
{
    /**
     * @var ObjectCollectionPromiseInterface<T,P>
     */
    private ObjectCollectionPromiseInterface $promise;

    /**
     * @param ObjectCollectionPromiseInterface<T,P> $promise
     */
    public function __construct(ObjectCollectionPromiseInterface $promise)
    {
        $this->promise = $promise;
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

    /**
     * @return DoctrineObjectCollection<T,P>
     */
    public function __resolve(): DoctrineObjectCollection
    {
        $this->initialize();

        // @phpstan-ignore-next-line
        return $this->collection;
    }

    public function getScope(): ScopeInterface
    {
        return $this->promise->getScope();
    }

    /**
     * @param ScopeInterface $scope
     * @return self<T,P>
     */
    public function withScope(ScopeInterface $scope): self
    {
        $scoped = $this->promise->withScope($scope);
        return $scoped === $this->promise ? $this : new self($scoped);
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

    protected function doInitialize(): void
    {
        $collection = $this->promise->__resolve();

        if (!$collection instanceof DoctrineObjectCollection) {
            $collection = new DoctrineObjectCollection($collection, $collection->getPivotContext());
        }

        /** @phpstan-var DoctrineObjectCollection<T,P> $collection */
        $this->collection = $collection;
    }
}
