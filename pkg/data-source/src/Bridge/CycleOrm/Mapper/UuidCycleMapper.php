<?php

declare(strict_types=1);

namespace Warp\DataSource\Bridge\CycleOrm\Mapper;

use Cycle\ORM\Exception\MapperException;
use Throwable;
use Warp\ValueObject\UuidValue;

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
