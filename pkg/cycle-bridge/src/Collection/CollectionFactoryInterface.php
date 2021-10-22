<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection;

use Cycle\ORM\Heap\Node;
use spaceonfire\Bridge\Cycle\Collection\Relation\ToManyRelationInterface;

interface CollectionFactoryInterface
{
    /**
     * @param ToManyRelationInterface $relation
     * @param iterable<mixed> $elements
     * @return ObjectCollectionInterface<object,mixed>
     */
    public function initCollection(
        ToManyRelationInterface $relation,
        iterable $elements
    ): ObjectCollectionInterface;

    /**
     * @param ToManyRelationInterface $relation
     * @param Node $parentNode
     * @return ObjectCollectionPromiseInterface<object,mixed>|null
     */
    public function promiseCollection(
        ToManyRelationInterface $relation,
        Node $parentNode
    ): ?ObjectCollectionPromiseInterface;
}
