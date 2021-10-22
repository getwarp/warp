<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection;

use Cycle\ORM\Promise\PromiseInterface;
use Cycle\ORM\Select\ScopeInterface;

/**
 * @template T of object
 * @template P
 * @extends ObjectCollectionInterface<T,P>
 */
interface ObjectCollectionPromiseInterface extends ObjectCollectionInterface, PromiseInterface, \Countable
{
    public function __id(): int;

    /**
     * @inheritDoc
     * @phpstan-return array<string,mixed>
     */
    public function __scope(): array;

    /**
     * @inheritDoc
     * @phpstan-return ObjectCollectionInterface<T,P>
     */
    public function __resolve(): ObjectCollectionInterface;

    public function getScope(): ScopeInterface;

    /**
     * @param ScopeInterface $scope
     * @return self<T,P>
     */
    public function withScope(ScopeInterface $scope): self;
}
