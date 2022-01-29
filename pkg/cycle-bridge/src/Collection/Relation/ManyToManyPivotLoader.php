<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Relation;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Promise\PromiseInterface;
use Cycle\ORM\Promise\ReferenceInterface;
use Cycle\ORM\Select;
use spaceonfire\Bridge\Cycle\NodeHelper;
use Spiral\Database\Injection\Parameter;

/**
 * @internal
 */
final class ManyToManyPivotLoader
{
    private ORMInterface $orm;

    private string $role;

    private string $innerKey;

    private string $outerKey;

    private string $throughInnerKey;

    private string $throughOuterKey;

    private Node $innerNode;

    /**
     * @var Node[]
     */
    private array $outerNodes = [];

    private bool $loaded = false;

    /**
     * @var array<string,object>
     */
    private array $loadedPivots = [];

    public function __construct(
        ORMInterface $orm,
        string $role,
        string $innerKey,
        string $outerKey,
        string $throughInnerKey,
        string $throughOuterKey,
        Node $innerNode
    ) {
        $this->orm = $orm;
        $this->role = $this->orm->resolveRole($role);
        $this->innerKey = $innerKey;
        $this->outerKey = $outerKey;
        $this->throughInnerKey = $throughInnerKey;
        $this->throughOuterKey = $throughOuterKey;
        $this->innerNode = $innerNode;
    }

    public function addOuterNode(Node $node): void
    {
        if ($this->loaded) {
            throw new \RuntimeException('Cannot add outer nodes after pivot context loaded.');
        }

        $this->outerNodes[] = $node;
    }

    /**
     * @param Node $outerNode
     * @param mixed $pivot
     * @return object
     */
    public function getPivot(Node $outerNode, $pivot = null): object
    {
        $this->loadPivotContext();

        $offset = $this->fetchKey($outerNode, $this->outerKey);

        $realPivot = null === $offset ? null : $this->loadedPivots[$offset] ?? null;

        return $this->hydratePivot($this->initPivot($outerNode, $realPivot ?? $pivot), $pivot);
    }

    private function loadPivotContext(): void
    {
        if ($this->loaded) {
            return;
        }

        if (null === $where = $this->makeWhereScope()) {
            return;
        }

        $select = new Select($this->orm, $this->role);
        $select->where($where);

        $this->loaded = true;
        $this->loadedPivots = $this->indexPivots($select);
        $this->outerNodes = [];
    }

    /**
     * @return array<string,mixed>|null
     */
    private function makeWhereScope(): ?array
    {
        if (!NodeHelper::nodePersisted($this->innerNode)) {
            return null;
        }

        if (null === $innerValue = $this->fetchKey($this->innerNode, $this->innerKey)) {
            return null;
        }

        $outerValues = [];
        foreach ($this->outerNodes as $outerNode) {
            if (!NodeHelper::nodePersisted($outerNode)) {
                continue;
            }

            if (null === $outerValue = $this->fetchKey($outerNode, $this->outerKey)) {
                continue;
            }

            $outerValues[] = $outerValue;
        }

        if ([] === $outerValues) {
            return null;
        }

        return [
            $this->throughInnerKey => $innerValue,
            $this->throughOuterKey => new Parameter($outerValues),
        ];
    }

    /**
     * @param object[] $pivots
     * @return object[]
     */
    private function indexPivots(iterable $pivots): array
    {
        $output = [];

        foreach ($pivots as $pivot) {
            $node = $this->getNode($pivot);
            $offset = $this->fetchKey($node, $this->throughOuterKey);
            \assert(null !== $offset);
            $output[$offset] = $pivot;
        }

        return $output;
    }

    /**
     * @param Node $node
     * @param string $key
     * @return mixed
     */
    private function fetchKey(Node $node, string $key)
    {
        return $node->getData()[$key] ?? null;
    }

    /**
     * Since many-to-many relation can overlap from two directions we have to properly resolve the pivot entity upon
     * its generation. This is achieved using temporary mapping associated with each of the entity states.
     * @param Node $outerNode
     * @param mixed $pivot
     * @return object
     */
    private function initPivot(Node $outerNode, $pivot): object
    {
        [$source, $target] = $this->sortRelation($this->innerNode, $outerNode);

        $relationStorage = $source->getState()->getStorage($this->role);

        if ($relationStorage->contains($target)) {
            return $relationStorage->offsetGet($target);
        }

        $pivot = $this->makePivot($pivot);

        $pNode = $this->getNode($pivot, $this->role);
        if (null !== $offset = $this->fetchKey($pNode, $this->throughOuterKey)) {
            $this->loadedPivots[(string)$offset] = $pivot;
        }

        $relationStorage->offsetSet($target, $pivot);

        return $pivot;
    }

    /**
     * Keep only one relation branch as primary branch.
     * @param Node $node
     * @param Node $related
     * @return array{Node,Node}
     */
    private function sortRelation(Node $node, Node $related): array
    {
        // always use single storage
        if ($related->getState()->getStorage($this->role)->contains($node)) {
            return [$related, $node];
        }

        return [$node, $related];
    }

    /**
     * @param mixed $pivot
     * @return object
     */
    private function makePivot($pivot): object
    {
        if (\is_array($pivot) || null === $pivot) {
            $pivot = $this->orm->make($this->role, $pivot ?? []);
            \assert(null !== $pivot);
        } elseif (!\is_object($pivot)) {
            throw new \RuntimeException(\sprintf(
                'Argument #3 ($pivot) expected to be an object, array or null. Got: %s.',
                \get_debug_type($pivot)
            ));
        }

        return $pivot;
    }

    /**
     * @param object $pivot
     * @param mixed $data
     * @return object
     */
    private function hydratePivot(object $pivot, $data): object
    {
        if (null === $data) {
            return $pivot;
        }

        $mapper = $this->orm->getMapper($this->role);

        if (\is_object($data)) {
            $data = $mapper->extract($data);
        }

        if (!\is_array($data)) {
            return $pivot;
        }

        return $mapper->hydrate($pivot, $data);
    }

    private function getNode(object $entity, ?string $role = null): Node
    {
        if ($entity instanceof PromiseInterface && $entity->__loaded()) {
            $entity = $entity->__resolve();
        }

        if ($entity instanceof ReferenceInterface) {
            return new Node(Node::PROMISED, $entity->__scope(), $entity->__role());
        }

        $node = $this->orm->getHeap()->get($entity);

        if (null === $node) {
            // possibly rely on relation target role, it will allow context switch
            $node = new Node(Node::NEW, [], $role ?? $this->orm->resolveRole($entity));
            $this->orm->getHeap()->attach($entity, $node);
        }

        return $node;
    }
}
