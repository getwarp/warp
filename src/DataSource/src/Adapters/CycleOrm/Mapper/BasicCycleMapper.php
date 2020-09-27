<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Mapper;

use function class_alias;

class_alias(
    \spaceonfire\DataSource\Bridge\CycleOrm\Mapper\BasicCycleMapper::class,
    __NAMESPACE__ . '\BasicCycleMapper'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\DataSource\Bridge\CycleOrm\Mapper\BasicCycleMapper instead.
     */
    class BasicCycleMapper extends \spaceonfire\DataSource\Bridge\CycleOrm\Mapper\BasicCycleMapper
    {
    }
}
