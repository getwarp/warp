<?php

declare(strict_types=1);

namespace Warp\DataSource\Adapters\CycleOrm\Query;

use function class_alias;

class_alias(
    \Warp\DataSource\Bridge\CycleOrm\Query\CycleQueryExpressionVisitor::class,
    __NAMESPACE__ . '\CycleQueryExpressionVisitor'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\DataSource\Bridge\CycleOrm\Query\CycleQueryExpressionVisitor instead.
     */
    class CycleQueryExpressionVisitor extends \Warp\DataSource\Bridge\CycleOrm\Query\CycleQueryExpressionVisitor
    {
    }
}
