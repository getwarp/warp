<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Relation;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\Relation\ChangesCheckerInterface;
use Cycle\ORM\Relation\RelationInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionPromiseInterface;
use spaceonfire\Bridge\Cycle\Select\ReferenceScope;

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
     * @return int
     */
    public function countCollection(ObjectCollectionPromiseInterface $collection): int;
}
