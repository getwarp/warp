<?php

declare(strict_types=1);

namespace Warp\DataSource\Adapters\CycleOrm\Mapper;

use function class_alias;

class_alias(
    \Warp\DataSource\Bridge\CycleOrm\Mapper\UuidCycleMapper::class,
    __NAMESPACE__ . '\UuidCycleMapper'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\DataSource\Bridge\CycleOrm\Mapper\UuidCycleMapper instead.
     */
    class UuidCycleMapper extends \Warp\DataSource\Bridge\CycleOrm\Mapper\UuidCycleMapper
    {
    }
}
