<?php

declare(strict_types=1);

namespace Warp\DataSource\Adapters\CycleOrm\Collection;

use function class_alias;

class_alias(
    \Warp\DataSource\Bridge\CycleOrm\Collection\PivotAwareTrait::class,
    __NAMESPACE__ . '\PivotAwareTrait'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\DataSource\Bridge\CycleOrm\Collection\PivotAwareTrait instead.
     */
    trait PivotAwareTrait
    {
        use \Warp\DataSource\Bridge\CycleOrm\Collection\PivotAwareTrait;
    }
}
