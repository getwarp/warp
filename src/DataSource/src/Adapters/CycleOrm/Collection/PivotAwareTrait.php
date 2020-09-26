<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Collection;

use function class_alias;

class_alias(
    \spaceonfire\DataSource\Bridge\CycleOrm\Collection\PivotAwareTrait::class,
    __NAMESPACE__ . '\PivotAwareTrait'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\DataSource\Bridge\CycleOrm\Collection\PivotAwareTrait instead.
     */
    trait PivotAwareTrait
    {
        use \spaceonfire\DataSource\Bridge\CycleOrm\Collection\PivotAwareTrait;
    }
}
