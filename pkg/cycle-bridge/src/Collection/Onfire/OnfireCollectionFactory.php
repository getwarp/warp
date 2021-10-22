<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Onfire;

use Cycle\ORM\Heap\Node;
use spaceonfire\Bridge\Cycle\Collection\Basic\BasicCollectionFactory;
use spaceonfire\Bridge\Cycle\Collection\CollectionFactoryInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionPromiseInterface;
use spaceonfire\Bridge\Cycle\Collection\Relation\ToManyRelationInterface;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Exception\PackageMissingException;

final class OnfireCollectionFactory implements CollectionFactoryInterface
{
    private BasicCollectionFactory $factory;

    public function __construct()
    {
        if (!\interface_exists(CollectionInterface::class)) {
            throw PackageMissingException::new('spaceonfire/collection', null, self::class);
        }

        $this->factory = new BasicCollectionFactory();
    }

    public function initCollection(ToManyRelationInterface $relation, iterable $elements): ObjectCollectionInterface
    {
        return OnfireObjectCollection::new($this->factory->initCollection($relation, $elements));
    }

    public function promiseCollection(
        ToManyRelationInterface $relation,
        Node $parentNode
    ): ?ObjectCollectionPromiseInterface {
        $promise = $this->factory->promiseCollection($relation, $parentNode);
        return null === $promise ? null : OnfireObjectCollectionPromise::new($promise);
    }
}
