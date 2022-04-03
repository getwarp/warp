<?php

declare(strict_types=1);

namespace Warp\DataSource\Adapters\CycleOrm\Mapper\Hydrator;

use function class_alias;

class_alias(
    \Warp\DataSource\Bridge\CycleOrm\Mapper\Hydrator\StdClassHydrator::class,
    __NAMESPACE__ . '\StdClassHydrator'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\DataSource\Bridge\CycleOrm\Mapper\Hydrator\StdClassHydrator instead.
     */
    class StdClassHydrator extends \Warp\DataSource\Bridge\CycleOrm\Mapper\Hydrator\StdClassHydrator
    {
    }
}
