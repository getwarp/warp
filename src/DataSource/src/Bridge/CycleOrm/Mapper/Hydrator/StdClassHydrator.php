<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Bridge\CycleOrm\Mapper\Hydrator;

use function class_alias;

class_alias(\spaceonfire\LaminasHydratorBridge\StdClassHydrator::class, __NAMESPACE__ . '\StdClassHydrator');

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\LaminasHydratorBridge\StdClassHydrator instead.
     */
    class StdClassHydrator extends \spaceonfire\LaminasHydratorBridge\StdClassHydrator
    {
    }
}
