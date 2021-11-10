<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Relation;

use Cycle\ORM\Command\Branch\Nil;
use Cycle\ORM\Command\Branch\Sequence;
use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Command\ContextCarrierInterface as CC;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Iterator;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Parser\RootNode;
use Cycle\ORM\Relation;
use Cycle\ORM\Select\AbstractLoader;
use Cycle\ORM\Select\Loader\ManyToManyLoader;
use Cycle\ORM\Select\RootLoader;
use Cycle\ORM\Select\ScopeInterface;
use spaceonfire\Bridge\Cycle\Collection\Change;
use spaceonfire\Bridge\Cycle\Collection\ChangesEnabledInterface;
use spaceonfire\Bridge\Cycle\Collection\CollectionFactoryInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionPromiseInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectStorage;
use spaceonfire\Bridge\Cycle\NodeHelper;
use spaceonfire\Bridge\Cycle\Select\PrepareLoaderScopeInterface;
use spaceonfire\Bridge\Cycle\Select\ReferenceScope;
use spaceonfire\Bridge\Cycle\Select\ScopeAggregate;
use Spiral\Database\Query\SelectQuery;

class ManyToMany extends AbstractToManyRelation
{
    protected string $pivotEntity;

    protected string $throughInnerKey;

    protected string $throughOuterKey;

    public function __construct(
        ORMInterface $orm,
        string $name,
        string $target,
        array $schema,
        CollectionFactoryInterface $collectionFactory
    ) {
        parent::__construct($orm, $name, $target, $schema, $collectionFactory);

        $this->pivotEntity = $this->schema[Relation::THROUGH_ENTITY];
        $this->throughInnerKey = $this->schema[Relation::THROUGH_INNER_KEY];
        $this->throughOuterKey = $this->schema[Relation::THROUGH_OUTER_KEY];
    }

    /**
     * @param Node $node
     * @param mixed[] $data
     * @return array{ObjectCollectionInterface<object,mixed>,ObjectStorage<object,mixed>}
     */
    public function init(Node $node, array $data): array
    {
        $collection = $this->initCollection($data);

        return [$collection, ObjectStorage::snapshot($collection)];
    }

    /**
     * @param Node $node
     * @return array{ObjectCollectionInterface<object,mixed>,ObjectCollectionPromiseInterface<object,mixed>|ObjectStorage<object,mixed>}
     */
    public function initPromise(Node $node): array
    {
        $p = $this->collectionFactory->promiseCollection($this, $node);

        if (null === $p) {
            return $this->init($node, []);
        }

        $this->linkNodeToPromise($node, $p);

        /** @phpstan-var ObjectCollectionPromiseInterface<object,mixed> $p */
        return [$p, $p];
    }

    /**
     * @param mixed $data
     * @return ObjectCollectionPromiseInterface<object,mixed>|ObjectStorage<object,mixed>
     */
    public function extract($data)
    {
        if ($data instanceof ObjectCollectionPromiseInterface && !$data->__loaded()) {
            return $data;
        }

        return ObjectStorage::snapshot($data);
    }

    public function makeReferenceScope(Node $parentNode): ?ReferenceScope
    {
        $value = $this->fetchKey($parentNode, $this->innerKey);

        if (null === $value || NodeHelper::nodeNew($parentNode)) {
            return null;
        }

        $scope = [
            $this->throughInnerKey => $value,
        ];

        return new ReferenceScope(
            $scope,
            $this->schema[Relation::WHERE] ?? [],
            $this->schema[Relation::ORDER_BY] ?? [],
        );
    }

    public function loadCollection(ObjectCollectionPromiseInterface $collection): ObjectCollectionInterface
    {
        $innerKey = $collection->__scope()[$this->throughInnerKey] ?? null;
        if (null === $innerKey) {
            return $this->collectionFactory->initCollection($this, []);
        }

        $loader = $this->makeCollectionLoader($collection->getScope());
        $query = $this->makeQuery($loader, $innerKey);

        // we are going to add pivot node into virtual root node (only ID) to aggregate the results
        $root = new RootNode([$this->innerKey], $this->innerKey);

        $node = $loader->createNode();
        $root->linkNode('output', $node);

        // emulate presence of parent entity
        $root->parseRow(0, [$innerKey]);

        $stmt = $query->run();
        foreach ($stmt as $row) {
            $node->parseRow(0, $row);
        }
        $stmt->close();

        // load all eager relations, forbid loader to re-fetch data (make it think it was joined)
        $loader->withContext($loader, [
            'method' => AbstractLoader::INLOAD,
        ])->loadData($node);

        $output = $this->initCollection($root->getResult()[0]['output']);

        $this->linkPromiseSnapshot($collection, $output);

        return $output;
    }

    public function countCollection(ObjectCollectionPromiseInterface $collection): int
    {
        $innerKey = $collection->__scope()[$this->throughInnerKey] ?? null;
        if (null === $innerKey) {
            return 0;
        }

        $loader = $this->makeCollectionLoader($collection->getScope());
        $query = $this->makeQuery($loader, $innerKey);

        return $query->count();
    }

    public function hasChanges($related, $original): bool
    {
        return parent::hasChanges($related, $original)
            || ($related instanceof ChangesEnabledInterface && $related->hasChanges());
    }

    /**
     * @param CC $store
     * @param object $entity
     * @param Node $node
     * @param ObjectCollectionPromiseInterface<object,mixed>|ObjectStorage<object,mixed> $related
     * @param ObjectCollectionPromiseInterface<object,mixed>|ObjectStorage<object,mixed>|null $original
     * @return CommandInterface
     */
    public function queue(CC $store, $entity, Node $node, $related, $original): CommandInterface
    {
        if ($related === $original && $related instanceof ChangesEnabledInterface) {
            return $this->queueCollectionChanges($node, $related);
        }

        return $this->queueForceSync($node, $related, $original);
    }

    /**
     * @param Node $node
     * @param ObjectCollectionPromiseInterface<object,mixed>|ObjectStorage<object,mixed> $related
     * @param ObjectCollectionPromiseInterface<object,mixed>|ObjectStorage<object,mixed>|null $original
     * @return CommandInterface
     */
    protected function queueForceSync(Node $node, $related, $original): CommandInterface
    {
        $sequence = new Sequence();

        $pivotLoader = new ManyToManyPivotLoader(
            $this->orm,
            $this->pivotEntity,
            $this->innerKey,
            $this->outerKey,
            $this->throughInnerKey,
            $this->throughOuterKey,
            $node,
        );

        $relatedStorage = ObjectStorage::snapshot($related);
        $originalStorage = ObjectStorage::snapshot($original);

        foreach ($relatedStorage as $item) {
            $outerNode = $this->getNode($item);
            \assert(null !== $outerNode);
            $pivot = $pivotLoader->getPivot(
                $outerNode,
                $relatedStorage->getPivot($item) ?? $originalStorage->getPivot($item)
            );
            $originalStorage->detach($item);
            $sequence->addCommand($this->queueLink($node, $item, $pivot));
            // update the link
            $related->setPivot($item, $pivot);
        }

        foreach ($originalStorage as $item) {
            if (null === $pivot = $originalStorage->getPivot($item)) {
                continue;
            }

            $sequence->addCommand($this->queueUnlink($item, $pivot));
        }

        return $sequence;
    }

    /**
     * @param Node $node
     * @param ChangesEnabledInterface<object,mixed> $related
     * @return CommandInterface
     */
    protected function queueCollectionChanges(Node $node, ChangesEnabledInterface $related): CommandInterface
    {
        if (!$related->hasChanges()) {
            return new Nil();
        }

        $sequence = new Sequence();

        $pivotLoader = new ManyToManyPivotLoader(
            $this->orm,
            $this->pivotEntity,
            $this->innerKey,
            $this->outerKey,
            $this->throughInnerKey,
            $this->throughOuterKey,
            $node,
        );

        $changes = $related->releaseChanges();

        foreach ($changes as $change) {
            $outerNode = $this->getNode($change->getElement());
            \assert(null !== $outerNode);
            $pivotLoader->addOuterNode($outerNode);
        }

        foreach ($changes as $change) {
            $outerNode = $this->getNode($change->getElement());
            \assert(null !== $outerNode);

            $pivot = $pivotLoader->getPivot($outerNode, $change->getPivot());

            switch ($change->getType()) {
                case Change::ADD:
                    $sequence->addCommand($this->queueLink($node, $change->getElement(), $pivot));
                    break;

                case Change::REMOVE:
                    $sequence->addCommand($this->queueUnlink($change->getElement(), $pivot));
                    break;
            }
        }

        return 0 === $sequence->count() ? new Nil() : $sequence;
    }

    protected function queueLink(Node $node, object $related, object $pivot): CommandInterface
    {
        $rNode = $this->getNode($related, +1);
        \assert(null !== $rNode);
        $this->assertValid($rNode);
        $rStore = $this->orm->queueStore($related);

        $pNode = $this->getNode($pivot);
        \assert(null !== $pNode);

        // defer the insert until pivot keys are resolved
        $pStore = $this->orm->queueStore($pivot);

        $this->forwardContext(
            $node,
            $this->innerKey,
            $pStore,
            $pNode,
            $this->throughInnerKey
        );

        $this->forwardContext(
            $rNode,
            $this->outerKey,
            $pStore,
            $pNode,
            $this->throughOuterKey
        );

        $sequence = new Sequence();
        $sequence->addCommand($rStore);
        $sequence->addCommand($pStore);

        return $sequence;
    }

    protected function queueUnlink(object $related, object $pivot): CommandInterface
    {
        $sequence = new Sequence();

        $pNode = $this->getNode($pivot);
        \assert(null !== $pNode);
        if (NodeHelper::nodePersisted($pNode)) {
            $sequence->addCommand($this->orm->queueDelete($pivot));
        } else {
            // Just mark a node deleted so that it can be removed from the heap.
            $pNode->getState()->setStatus(Node::SCHEDULED_DELETE);
            $pNode->getState()->decClaim();
        }

        $sequence->addCommand($this->orm->queueStore($related));

        return $sequence;
    }

    /**
     * @param mixed[] $data
     * @return ObjectCollectionInterface<object,mixed>
     */
    protected function initCollection(array $data): ObjectCollectionInterface
    {
        $storage = new ObjectStorage();

        $iterator = new Iterator($this->orm, $this->target, $data, true);
        foreach ($iterator as $pivot => $entity) {
            if (!\is_array($pivot)) {
                // skip partially selected entities (DB level filter)
                continue;
            }

            $storage->attach($entity, $this->orm->make($this->pivotEntity, $pivot, Node::MANAGED));
        }

        return $this->collectionFactory->initCollection($this, $storage);
    }

    private function makeCollectionLoader(ScopeInterface $scope): ManyToManyLoader
    {
        $role = $this->orm->resolveRole($this->target);
        if (null !== $sourceScope = $this->orm->getSource($role)->getConstrain()) {
            $scope = new ScopeAggregate($scope, $sourceScope);
        }

        $loader = new ManyToManyLoader(
            $this->orm,
            $this->orm->getSource($this->target)->getTable(),
            $this->target,
            $this->schema,
        );

        $loader = $loader->withContext($loader, [
            'scope' => $scope,
            'as' => $this->target,
            'method' => AbstractLoader::POSTLOAD,
        ]);

        \assert($loader instanceof ManyToManyLoader);

        if ($scope instanceof PrepareLoaderScopeInterface) {
            $scope->prepareLoader($loader);
        }

        return $loader;
    }

    /**
     * @param ManyToManyLoader $loader
     * @param mixed $innerKey
     * @return SelectQuery<mixed>
     */
    private function makeQuery(ManyToManyLoader $loader, $innerKey): SelectQuery
    {
        return $loader->configureQuery(
            (new RootLoader($this->orm, $this->target))->buildQuery(),
            [$innerKey]
        );
    }
}
