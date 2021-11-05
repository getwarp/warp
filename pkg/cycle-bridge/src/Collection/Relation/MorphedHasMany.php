<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Relation;

use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Relation;
use spaceonfire\Bridge\Cycle\Collection\CollectionFactoryInterface;

class MorphedHasMany extends HasMany
{
    private string $morphKey;

    public function __construct(
        ORMInterface $orm,
        string $name,
        string $target,
        array $schema,
        CollectionFactoryInterface $collectionFactory
    ) {
        parent::__construct($orm, $name, $target, $schema, $collectionFactory);

        $this->morphKey = $schema[Relation::MORPH_KEY];
    }

    protected function queueStore(Node $node, object $related): ContextCarrierInterface
    {
        $rStore = parent::queueStore($node, $related);

        $rNode = $this->getNode($related);
        \assert(null !== $rNode);
        if ($this->fetchKey($rNode, $this->morphKey) !== $node->getRole()) {
            $rStore->waitContext($this->columnName($node, $this->morphKey), true);
            $rStore->register($this->columnName($node, $this->morphKey), $node->getRole(), true);
            $rNode->register($this->morphKey, $node->getRole(), true);
        }

        return $rStore;
    }

    protected function assertValid(Node $related): void
    {
        // no need to validate morphed relation yet
    }

    protected function areNodesLinked(Node $parentNode, Node $relatedNode): bool
    {
        return parent::areNodesLinked($parentNode, $relatedNode)
            && 0 === Node::compare($this->fetchKey($relatedNode, $this->morphKey), $parentNode->getRole());
    }

    protected function getWhereScope(Node $parentNode): array
    {
        $where = parent::getWhereScope($parentNode);

        if ([] !== $where) {
            $where[$this->morphKey] = $parentNode->getRole();
        }

        return $where;
    }
}
