<?php

declare(strict_types=1);

namespace Warp\DataSource\Bridge\CycleOrm\Mapper\Hydrator;

use function class_alias;

class_alias(\Warp\LaminasHydratorBridge\StdClassHydrator::class, __NAMESPACE__ . '\StdClassHydrator');

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\LaminasHydratorBridge\StdClassHydrator instead.
     */
    class StdClassHydrator extends \Warp\LaminasHydratorBridge\StdClassHydrator
    {
    }
}
