<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle;

use Cycle\ORM\Heap\Node;

abstract class NodeHelper
{
    public static function nodeNew(Node $node): bool
    {
        return \in_array($node->getStatus(), [Node::NEW, Node::SCHEDULED_INSERT], true);
    }

    public static function nodePersisted(Node $node): bool
    {
        return \in_array($node->getStatus(), [Node::MANAGED, Node::SCHEDULED_UPDATE], true);
    }
}
