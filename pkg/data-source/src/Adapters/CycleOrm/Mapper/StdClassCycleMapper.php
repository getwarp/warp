<?php

declare(strict_types=1);

namespace Warp\DataSource\Adapters\CycleOrm\Mapper;

use function class_alias;

class_alias(
    \Warp\DataSource\Bridge\CycleOrm\Mapper\StdClassCycleMapper::class,
    __NAMESPACE__ . '\StdClassCycleMapper'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\DataSource\Bridge\CycleOrm\Mapper\StdClassCycleMapper instead.
     */
    class StdClassCycleMapper extends \Warp\DataSource\Bridge\CycleOrm\Mapper\StdClassCycleMapper
    {
    }
}
