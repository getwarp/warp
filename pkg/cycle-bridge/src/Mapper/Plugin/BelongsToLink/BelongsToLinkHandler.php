<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin\BelongsToLink;

use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Promise\PromiseInterface;
use Cycle\ORM\Promise\ReferenceInterface;
use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\QueueAfterEvent;
use spaceonfire\Bridge\Cycle\NodeHelper;

final class BelongsToLinkHandler
{
    private ORMInterface $orm;

    private bool $loadReferences;

    public function __construct(ORMInterface $orm, bool $loadReferences = false)
    {
        $this->orm = $orm;
        $this->loadReferences = $loadReferences;
    }

    public function handle(QueueAfterEvent $event): void
    {
        $command = $event->getCommand();

        if (!$command instanceof ContextCarrierInterface) {
            return;
        }

        $node = $event->getNode();
        $relations = $this->orm->getSchema()->define($node->getRole(), SchemaInterface::RELATIONS);

        foreach ($relations as $key => $relation) {
            if (
                Relation::BELONGS_TO !== $relation[Relation::TYPE] &&
                Relation::REFERS_TO !== $relation[Relation::TYPE] &&
                Relation::BELONGS_TO_MORPHED !== $relation[Relation::TYPE]
            ) {
                continue;
            }

            $extractedData ??= $this->orm->getMapper($node->getRole())->extract($event->getEntity());
            $related = $extractedData[$key] ?? null;

            if (!\is_object($related)) {
                continue;
            }

            $innerKey = $relation[Relation::SCHEMA][Relation::INNER_KEY];
            $outerKey = $relation[Relation::SCHEMA][Relation::OUTER_KEY];
            $morphKey = $relation[Relation::SCHEMA][Relation::MORPH_KEY] ?? null;
            $nullable = $relation[Relation::NULLABLE] ?? false;

            if ($related instanceof ReferenceInterface) {
                $this->linkByReference($command, $node, $related, $innerKey, $outerKey, $morphKey, $nullable);
            } else {
                $this->linkByEntity($command, $node, $related, $innerKey, $outerKey, $morphKey, $nullable);
            }
        }
    }

    private function columnName(Node $node, string $field): string
    {
        return $this->orm->getSchema()->define($node->getRole(), SchemaInterface::COLUMNS)[$field] ?? $field;
    }

    private function linkByReference(
        ContextCarrierInterface $command,
        Node $node,
        ReferenceInterface $related,
        string $innerKey,
        string $outerKey,
        ?string $morphKey,
        bool $nullable
    ): void {
        if (null !== $entity = $this->orm->get($related->__role(), $related->__scope(), false)) {
            $this->linkByEntity($command, $node, $entity, $innerKey, $outerKey, $morphKey, $nullable);
            return;
        }

        $scope = $related->__scope();
        if (isset($scope[$outerKey])) {
            $innerColumn = $this->columnName($node, $innerKey);
            $command->waitContext($innerColumn, !$nullable);
            $node->forward($innerKey, $command, $innerColumn);
            $node->register($innerKey, $scope[$outerKey], true);

            if (null === $morphKey) {
                return;
            }

            $morphColumn = $this->columnName($node, $morphKey);
            $command->waitContext($morphColumn, !$nullable);
            $node->forward($morphKey, $command, $morphColumn);
            $node->register($morphKey, $related->__role(), true);
            return;
        }

        if (null !== $entity = $this->loadReference($related)) {
            $this->linkByEntity($command, $node, $entity, $innerKey, $outerKey, $morphKey, $nullable);
            return;
        }
    }

    private function linkByEntity(
        ContextCarrierInterface $command,
        Node $node,
        object $related,
        string $innerKey,
        string $outerKey,
        ?string $morphKey,
        bool $nullable
    ): void {
        $rNode = $this->orm->getHeap()->get($related);

        if (!NodeHelper::nodeIs($rNode, Node::MANAGED, Node::SCHEDULED_INSERT, Node::SCHEDULED_UPDATE)) {
            return;
        }
        \assert(null !== $rNode);

        // command <- node <- relNode
        $innerColumn = $this->columnName($node, $innerKey);
        $command->waitContext($innerColumn, !$nullable);
        $node->forward($innerKey, $command, $innerColumn);
        $rNode->forward($outerKey, $node, $innerKey, true);

        if (!\array_key_exists($outerKey, $rNode->getInitialData())) {
            $command->waitContext($innerColumn, !$nullable);
        }

        if (null === $morphKey) {
            return;
        }

        $morphColumn = $this->columnName($node, $morphKey);
        $command->waitContext($morphColumn, !$nullable);
        $node->forward($morphKey, $command, $morphColumn);
        $node->register($morphKey, $rNode->getRole(), true);
    }

    private function loadReference(ReferenceInterface $reference): ?object
    {
        if (!$this->loadReferences) {
            return null;
        }

        if ($reference instanceof PromiseInterface) {
            return $reference->__resolve();
        }

        return $this->orm->get($reference->__role(), $reference->__scope());
    }
}
