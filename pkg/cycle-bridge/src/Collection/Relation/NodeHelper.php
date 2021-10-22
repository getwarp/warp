<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Relation;

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

    /**
     * @param mixed $a
     * @param mixed $b
     * @return int
     * @link https://github.com/cycle/orm/blob/1e2ecea2afcc4d611cdd0c904578880f7457853a/src/Heap/Node.php#L176-L206
     */
    public static function compare($a, $b): int
    {
        if ($a === $b) {
            return 0;
        }
        if ($a != $b || null === $a || null === $b) {
            return 1;
        }

        $ta = [\gettype($a), \gettype($b)];

        // array, boolean, double, integer, string
        \sort($ta, \SORT_STRING);

        if ('string' === $ta[1]) {
            if ('' === $a || '' === $b) {
                return -1;
            }
            if (\in_array($ta[0], ['integer', 'double'], true)) {
                return (int)((string)$a !== (string)$b);
            }
        }

        if ('boolean' === $ta[0]) {
            $a = \filter_var($a, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE);
            $b = \filter_var($b, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE);
            return (int)($a !== $b);
        }

        return 1;
    }
}
