<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Relation;

use Cycle\ORM\Command\Branch\Condition;
use Cycle\ORM\Command\Branch\Nil;
use Cycle\ORM\Command\Branch\Sequence;
use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Command\ContextCarrierInterface as CC;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Iterator;
use Cycle\ORM\Parser\OutputNode;
use Cycle\ORM\Relation;
use Cycle\ORM\Select\RootLoader;
use Cycle\ORM\Select\ScopeInterface;
use spaceonfire\Bridge\Cycle\Collection\Change;
use spaceonfire\Bridge\Cycle\Collection\ChangesEnabledInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionPromiseInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectStorage;
use spaceonfire\Bridge\Cycle\Select\PrepareLoaderScopeInterface;
use spaceonfire\Bridge\Cycle\Select\ReferenceScope;
use spaceonfire\Bridge\Cycle\Select\ScopeAggregate;

class HasMany extends AbstractToManyRelation
{
    /**
     * @param Node $node
     * @param mixed[] $data
     * @return array{ObjectCollectionInterface<object,mixed>,ObjectStorage<object,mixed>}
     */
    public function init(Node $node, array $data): array
    {
        $elements = [];

        foreach ($data as $item) {
            if (\is_object($item)) {
                $itemNode = $this->getNode($item);
                \assert(null !== $itemNode);
                $this->assertValid($itemNode);
                $elements[] = $item;
                continue;
            }

            if (\is_array($item)) {
                $elements[] = $this->orm->make($this->target, $item, Node::MANAGED);
                continue;
            }

            throw new \InvalidArgumentException('Invalid $data');
        }

        /** @phpstan-var ObjectCollectionInterface<object,mixed> $collection */
        $collection = $this->collectionFactory->initCollection($this, $elements);

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
        if ([] === $scope = $this->getWhereScope($parentNode)) {
            return null;
        }

        /** @phpstan-var array<string,mixed> $where */
        $where = \array_merge($this->schema[Relation::WHERE] ?? [], $scope);
        $order = $this->schema[Relation::ORDER_BY] ?? [];

        return new ReferenceScope($scope, $where, $order);
    }

    public function loadCollection(ObjectCollectionPromiseInterface $collection): ObjectCollectionInterface
    {
        if ([] === $collection->__scope()) {
            return $this->collectionFactory->initCollection($this, []);
        }

        $loader = $this->makeCollectionLoader($collection->getScope());

        $node = $loader->createNode();
        \assert($node instanceof OutputNode);
        $loader->loadData($node);

        $output = $this->collectionFactory->initCollection(
            $this,
            new Iterator($this->orm, $loader->getTarget(), $node->getResult(), true),
        );

        $this->linkPromiseSnapshot($collection, $output);

        return $output;
    }

    public function countCollection(ObjectCollectionPromiseInterface $collection): int
    {
        if ([] === $collection->__scope()) {
            return 0;
        }

        return $this->makeCollectionLoader($collection->getScope())->buildQuery()->count();
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

        $related = ObjectStorage::snapshot($related);
        $original = ObjectStorage::snapshot($original);

        foreach ($related as $item) {
            $original->detach($item);
            $sequence->addCommand($this->queueStore($node, $item));
        }

        foreach ($original as $item) {
            $sequence->addCommand($this->queueDelete($node, $item));
        }

        return 0 < $sequence->count() ? $sequence : new Nil();
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

        foreach ($related->releaseChanges() as $change) {
            switch ($change->getType()) {
                case Change::ADD:
                    $sequence->addCommand($this->queueStore($node, $change->getElement()));
                    break;

                case Change::REMOVE:
                    $sequence->addCommand($this->queueDelete($node, $change->getElement()));
                    break;
            }
        }

        return 0 === $sequence->count() ? new Nil() : $sequence;
    }

    protected function queueStore(Node $node, object $related): CC
    {
        $rNode = $this->getNode($related, +1);
        \assert(null !== $rNode);
        $this->assertValid($rNode);

        $rStore = $this->orm->queueStore($related);

        $this->forwardContext(
            $node,
            $this->innerKey,
            $rStore,
            $rNode,
            $this->outerKey
        );

        return $rStore;
    }

    protected function queueDelete(Node $node, object $related): CommandInterface
    {
        $rNode = $this->getNode($related);
        \assert(null !== $rNode);

        // trying to delete not linked earlier entity.
        if (!$this->areNodesLinked($node, $rNode)) {
            return new Nil();
        }

        if ($this->isNullable()) {
            $command = $this->orm->queueStore($related);
            $command->waitContext($this->columnName($rNode, $this->outerKey), true);
            $command->register($this->columnName($rNode, $this->outerKey), null, true);
            $rNode->getState()->decClaim();
        } else {
            $command = $this->orm->queueDelete($related);
        }

        return new Condition($command, fn () => !$rNode->getState()->hasClaims());
    }

    protected function areNodesLinked(Node $parentNode, Node $relatedNode): bool
    {
        $this->assertValid($relatedNode);

        $left = $this->fetchKey($parentNode, $this->innerKey);
        $right = $this->fetchKey($relatedNode, $this->outerKey);

        return 0 === NodeHelper::compare($left, $right);
    }

    /**
     * @param Node $parentNode
     * @return array<string,mixed>
     */
    protected function getWhereScope(Node $parentNode): array
    {
        $value = $this->fetchKey($parentNode, $this->innerKey);

        if (null === $value || NodeHelper::nodeNew($parentNode)) {
            return [];
        }

        return [
            $this->outerKey => $value,
        ];
    }

    private function makeCollectionLoader(ScopeInterface $scope): RootLoader
    {
        $role = $this->orm->resolveRole($this->target);
        if (null !== $sourceScope = $this->orm->getSource($role)->getConstrain()) {
            $scope = new ScopeAggregate($scope, $sourceScope);
        }

        $loader = new RootLoader($this->orm, $role);
        $loader->setScope($scope);

        if ($scope instanceof PrepareLoaderScopeInterface) {
            $scope->prepareLoader($loader);
        }

        return $loader;
    }
}
