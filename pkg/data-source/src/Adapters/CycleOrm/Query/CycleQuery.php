<?php

declare(strict_types=1);

namespace Warp\DataSource\Adapters\CycleOrm\Query;

use function class_alias;

class_alias(\Warp\DataSource\Bridge\CycleOrm\Query\CycleQuery::class, __NAMESPACE__ . '\CycleQuery');

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\DataSource\Bridge\CycleOrm\Query\CycleQuery instead.
     */
    class CycleQuery extends \Warp\DataSource\Bridge\CycleOrm\Query\CycleQuery
    {
    }
}
