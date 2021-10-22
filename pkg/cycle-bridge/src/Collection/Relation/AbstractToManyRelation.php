<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Relation;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Relation;
use Cycle\ORM\Relation\AbstractRelation;
use spaceonfire\Bridge\Cycle\Collection\CollectionFactoryInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionPromiseInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectStorage;

abstract class AbstractToManyRelation extends AbstractRelation implements ToManyRelationInterface
{
    protected CollectionFactoryInterface $collectionFactory;

    /**
     * @phpstan-var ORMInterface
     */
    protected $orm;

    /**
     * @var array<int,\WeakReference<Node>>
     */
    private array $promiseNodeMap = [];

    /**
     * @param ORMInterface $orm
     * @param string $name
     * @param string $target
     * @param array<array-key,mixed> $schema
     * @param CollectionFactoryInterface $collectionFactory
     */
    public function __construct(
        ORMInterface $orm,
        string $name,
        string $target,
        array $schema,
        CollectionFactoryInterface $collectionFactory
    ) {
        parent::__construct($orm, $name, $target, $schema);

        $this->collectionFactory = $collectionFactory;
    }

    public function getRelationType(): int
    {
        return $this->schema[Relation::TYPE];
    }

    /**
     * @param Node $node
     * @param ObjectCollectionPromiseInterface<object,mixed> $promise
     * @internal
     */
    protected function linkNodeToPromise(Node $node, ObjectCollectionPromiseInterface $promise): void
    {
        $this->promiseNodeMap[$promise->__id()] = \WeakReference::create($node);

        foreach ($this->promiseNodeMap as $k => $ref) {
            if (null !== $ref->get()) {
                continue;
            }
            unset($this->promiseNodeMap[$k]);
        }
    }

    /**
     * @param ObjectCollectionPromiseInterface<object,mixed> $promise
     * @return Node|null
     * @internal
     */
    protected function getNodeByPromise(ObjectCollectionPromiseInterface $promise): ?Node
    {
        $ref = $this->promiseNodeMap[$promise->__id()] ?? null;
        return null === $ref ? null : $ref->get();
    }

    /**
     * Override promise in relation in node with loaded snapshot.
     * @param ObjectCollectionPromiseInterface<object,mixed> $promise
     * @param ObjectCollectionInterface<object,mixed> $collection
     * @internal
     */
    protected function linkPromiseSnapshot(
        ObjectCollectionPromiseInterface $promise,
        ObjectCollectionInterface $collection
    ): void {
        $node = $this->getNodeByPromise($promise);

        // it's ok that node is nullable, because relation can load cloned collection via withScope() method.
        if (null === $node) {
            return;
        }

        $node->setRelation($this->name, ObjectStorage::snapshot($collection));
    }
}
