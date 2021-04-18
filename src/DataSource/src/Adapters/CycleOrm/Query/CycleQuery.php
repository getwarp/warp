<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Query;

use function class_alias;

class_alias(\spaceonfire\DataSource\Bridge\CycleOrm\Query\CycleQuery::class, __NAMESPACE__ . '\CycleQuery');

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\DataSource\Bridge\CycleOrm\Query\CycleQuery instead.
     */
    class CycleQuery extends \spaceonfire\DataSource\Bridge\CycleOrm\Query\CycleQuery
    {
    }
}
