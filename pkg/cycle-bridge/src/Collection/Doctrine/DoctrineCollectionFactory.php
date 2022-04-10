<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Collection\Doctrine;

use Cycle\ORM\Heap\Node;
use Doctrine\Common\Collections\Collection;
use Warp\Bridge\Cycle\Collection\Basic\BasicCollectionFactory;
use Warp\Bridge\Cycle\Collection\CollectionFactoryInterface;
use Warp\Bridge\Cycle\Collection\Relation\ToManyRelationInterface;
use Warp\Exception\PackageMissingException;

final class DoctrineCollectionFactory implements CollectionFactoryInterface
{
    private BasicCollectionFactory $factory;

    public function __construct()
    {
        if (!\interface_exists(Collection::class)) {
            throw PackageMissingException::new('doctrine/collections', null, self::class);
        }

        $this->factory = new BasicCollectionFactory();
    }

    /**
     * @inheritDoc
     * @phpstan-return DoctrineObjectCollection<object,mixed>
     */
    public function initCollection(ToManyRelationInterface $relation, iterable $elements): DoctrineObjectCollection
    {
        $collection = $this->factory->initCollection($relation, $elements);

        return new DoctrineObjectCollection($collection, $collection->getPivotContext());
    }

    /**
     * @inheritDoc
     * @phpstan-return DoctrineObjectCollectionPromise<object,mixed>|null
     */
    public function promiseCollection(
        ToManyRelationInterface $relation,
        Node $parentNode
    ): ?DoctrineObjectCollectionPromise {
        $promise = $this->factory->promiseCollection($relation, $parentNode);
        return null === $promise ? null : new DoctrineObjectCollectionPromise($promise);
    }
}
