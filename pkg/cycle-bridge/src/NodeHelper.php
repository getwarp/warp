<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle;

use Cycle\ORM\Heap\Node;

abstract class NodeHelper
{
    public static function nodeIs(?Node $node, int $status, int ...$statuses): bool
    {
        if (null === $node) {
            return false;
        }

        return \in_array($node->getStatus(), [$status, ...$statuses], true);
    }

    public static function nodeNew(Node $node): bool
    {
        return self::nodeIs($node, Node::NEW, Node::SCHEDULED_INSERT);
    }

    public static function nodePersisted(Node $node): bool
    {
        return self::nodeIs($node, Node::MANAGED, Node::SCHEDULED_UPDATE);
    }
}
