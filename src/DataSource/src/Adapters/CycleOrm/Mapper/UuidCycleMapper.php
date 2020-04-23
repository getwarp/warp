<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Mapper;

use Cycle\ORM\Exception\MapperException;
use spaceonfire\ValueObject\UuidValue;
use Throwable;

class UuidCycleMapper extends BasicCycleMapper
{
    /**
     * @inheritDoc
     */
    protected function nextPrimaryKey()
    {
        try {
            return UuidValue::random()->value();
            // @codeCoverageIgnoreStart
        } catch (Throwable $e) {
            throw new MapperException($e->getMessage(), $e->getCode(), $e);
            // @codeCoverageIgnoreEnd
        }
    }
}
