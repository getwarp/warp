<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Collection\Warp;

use Cycle\ORM\Heap\Node;
use Warp\Bridge\Cycle\Collection\Basic\BasicCollectionFactory;
use Warp\Bridge\Cycle\Collection\CollectionFactoryInterface;
use Warp\Bridge\Cycle\Collection\ObjectCollectionInterface;
use Warp\Bridge\Cycle\Collection\ObjectCollectionPromiseInterface;
use Warp\Bridge\Cycle\Collection\Relation\ToManyRelationInterface;
use Warp\Collection\CollectionInterface;
use Warp\Exception\PackageMissingException;

final class WarpCollectionFactory implements CollectionFactoryInterface
{
    private BasicCollectionFactory $factory;

    public function __construct()
    {
        if (!\interface_exists(CollectionInterface::class)) {
            throw PackageMissingException::new('getwarp/collection', null, self::class);
        }

        $this->factory = new BasicCollectionFactory();
    }

    public function initCollection(ToManyRelationInterface $relation, iterable $elements): ObjectCollectionInterface
    {
        return WarpObjectCollection::new($this->factory->initCollection($relation, $elements));
    }

    public function promiseCollection(
        ToManyRelationInterface $relation,
        Node $parentNode
    ): ?ObjectCollectionPromiseInterface {
        $promise = $this->factory->promiseCollection($relation, $parentNode);
        return null === $promise ? null : WarpObjectCollectionPromise::new($promise);
    }
}
