<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Repository;

use function class_alias;

class_alias(
    \spaceonfire\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepository::class,
    __NAMESPACE__ . '\AbstractCycleRepository'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepository instead.
     */
    abstract class AbstractCycleRepository extends \spaceonfire\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepository
    {
    }
}
