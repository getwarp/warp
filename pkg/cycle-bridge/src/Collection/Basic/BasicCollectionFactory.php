<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Basic;

use Cycle\ORM\Heap\Node;
use spaceonfire\Bridge\Cycle\Collection\CollectionFactoryInterface;
use spaceonfire\Bridge\Cycle\Collection\Relation\ToManyRelationInterface;

final class BasicCollectionFactory implements CollectionFactoryInterface
{
    /**
     * @inheritDoc
     * @return BasicObjectCollection<object,mixed>
     */
    public function initCollection(ToManyRelationInterface $relation, iterable $elements): BasicObjectCollection
    {
        return new BasicObjectCollection($elements);
    }

    /**
     * @inheritDoc
     * @return BasicObjectCollectionPromise<object,mixed>|null
     */
    public function promiseCollection(
        ToManyRelationInterface $relation,
        Node $parentNode
    ): ?BasicObjectCollectionPromise {
        if (null === $scope = $relation->makeReferenceScope($parentNode)) {
            return null;
        }

        return new BasicObjectCollectionPromise($relation, $scope);
    }
}
