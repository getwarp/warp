<?php

declare(strict_types=1);

namespace Warp\DataSource\Adapters\CycleOrm\Collection;

use function class_alias;

class_alias(
    \Warp\DataSource\Bridge\CycleOrm\Collection\PivotAwareInterface::class,
    __NAMESPACE__ . '\PivotAwareInterface'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\DataSource\Bridge\CycleOrm\Collection\PivotAwareInterface instead.
     */
    interface PivotAwareInterface extends \Warp\DataSource\Bridge\CycleOrm\Collection\PivotAwareInterface
    {
    }
}
