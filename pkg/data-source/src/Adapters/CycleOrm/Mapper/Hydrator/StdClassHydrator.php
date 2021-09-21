<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Mapper\Hydrator;

use function class_alias;

class_alias(
    \spaceonfire\DataSource\Bridge\CycleOrm\Mapper\Hydrator\StdClassHydrator::class,
    __NAMESPACE__ . '\StdClassHydrator'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\DataSource\Bridge\CycleOrm\Mapper\Hydrator\StdClassHydrator instead.
     */
    class StdClassHydrator extends \spaceonfire\DataSource\Bridge\CycleOrm\Mapper\Hydrator\StdClassHydrator
    {
    }
}
