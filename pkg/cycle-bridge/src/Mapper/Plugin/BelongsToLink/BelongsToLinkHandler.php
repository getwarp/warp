<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin\BelongsToLink;

use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\QueueAfterEvent;
use spaceonfire\Bridge\Cycle\NodeHelper;

final class BelongsToLinkHandler
{
    private ORMInterface $orm;

    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
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

            $rNode = $this->orm->getHeap()->get($related);

            if (!NodeHelper::nodeIs($rNode, Node::MANAGED, Node::SCHEDULED_INSERT, Node::SCHEDULED_UPDATE)) {
                continue;
            }
            \assert(null !== $rNode);

            $innerKey = $relation[Relation::SCHEMA][Relation::INNER_KEY];
            $outerKey = $relation[Relation::SCHEMA][Relation::OUTER_KEY];
            $morphKey = $relation[Relation::SCHEMA][Relation::MORPH_KEY] ?? null;
            $nullable = $relation[Relation::NULLABLE] ?? false;

            // command <- node <- relNode
            $innerColumn = $this->columnName($node, $innerKey);
            $command->waitContext($innerColumn, !$nullable);
            $node->forward($innerKey, $command, $innerColumn);
            $rNode->forward($outerKey, $node, $innerKey, true);

            if (null === $morphKey) {
                continue;
            }

            $morphColumn = $this->columnName($node, $morphKey);
            $command->waitContext($morphColumn, !$nullable);
            $node->forward($morphKey, $command, $morphColumn);
            $node->register($morphKey, $rNode->getRole(), true);
        }
    }

    private function columnName(Node $node, string $field): string
    {
        return $this->orm->getSchema()->define($node->getRole(), SchemaInterface::COLUMNS)[$field] ?? $field;
    }
}
