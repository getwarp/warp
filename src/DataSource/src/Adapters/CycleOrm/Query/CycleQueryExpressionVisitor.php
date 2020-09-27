<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Query;

use function class_alias;

class_alias(
    \spaceonfire\DataSource\Bridge\CycleOrm\Query\CycleQueryExpressionVisitor::class,
    __NAMESPACE__ . '\CycleQueryExpressionVisitor'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\DataSource\Bridge\CycleOrm\Query\CycleQueryExpressionVisitor instead.
     */
    class CycleQueryExpressionVisitor extends \spaceonfire\DataSource\Bridge\CycleOrm\Query\CycleQueryExpressionVisitor
    {
    }
}
