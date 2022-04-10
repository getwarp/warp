<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Collection\Relation;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\Relation\ChangesCheckerInterface;
use Cycle\ORM\Relation\RelationInterface;
use Warp\Bridge\Cycle\Collection\ObjectCollectionInterface;
use Warp\Bridge\Cycle\Collection\ObjectCollectionPromiseInterface;
use Warp\Bridge\Cycle\Select\ReferenceScope;

interface ToManyRelationInterface extends RelationInterface, ChangesCheckerInterface
{
    public function getRelationType(): int;

    public function makeReferenceScope(Node $parentNode): ?ReferenceScope;

    /**
     * @param ObjectCollectionPromiseInterface<object,mixed> $collection
     * @return ObjectCollectionInterface<object,mixed>
     */
    public function loadCollection(ObjectCollectionPromiseInterface $collection): ObjectCollectionInterface;

    /**
     * @param ObjectCollectionPromiseInterface<object,mixed> $collection
     * @return int<0,max>
     */
    public function countCollection(ObjectCollectionPromiseInterface $collection): int;
}
