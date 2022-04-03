<?php

declare(strict_types=1);

namespace Warp\DataSource\Adapters\CycleOrm\Repository;

use function class_alias;

class_alias(
    \Warp\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepository::class,
    __NAMESPACE__ . '\AbstractCycleRepository'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepository instead.
     */
    abstract class AbstractCycleRepository extends \Warp\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepository
    {
    }
}
