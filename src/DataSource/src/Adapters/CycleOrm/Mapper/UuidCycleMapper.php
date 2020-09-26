<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Mapper;

use function class_alias;

class_alias(
    \spaceonfire\DataSource\Bridge\CycleOrm\Mapper\UuidCycleMapper::class,
    __NAMESPACE__ . '\UuidCycleMapper'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\DataSource\Bridge\CycleOrm\Mapper\UuidCycleMapper instead.
     */
    class UuidCycleMapper extends \spaceonfire\DataSource\Bridge\CycleOrm\Mapper\UuidCycleMapper
    {
    }
}
