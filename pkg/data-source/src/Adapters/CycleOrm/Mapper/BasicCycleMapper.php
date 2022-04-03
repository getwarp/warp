<?php

declare(strict_types=1);

namespace Warp\DataSource\Adapters\CycleOrm\Mapper;

use function class_alias;

class_alias(
    \Warp\DataSource\Bridge\CycleOrm\Mapper\BasicCycleMapper::class,
    __NAMESPACE__ . '\BasicCycleMapper'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\DataSource\Bridge\CycleOrm\Mapper\BasicCycleMapper instead.
     */
    class BasicCycleMapper extends \Warp\DataSource\Bridge\CycleOrm\Mapper\BasicCycleMapper
    {
    }
}
