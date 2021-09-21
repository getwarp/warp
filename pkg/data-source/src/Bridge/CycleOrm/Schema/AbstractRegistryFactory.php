<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Bridge\CycleOrm\Schema;

use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Registry;
use spaceonfire\DataSource\Bridge\CycleOrm\Mapper\BasicCycleMapper;
use spaceonfire\DataSource\Bridge\CycleOrm\Mapper\StdClassCycleMapper;

abstract class AbstractRegistryFactory
{
    abstract public function make(): Registry;

    protected function autocompleteEntity(Entity $e): void
    {
        if (null === $e->getRole() && null !== $class = $e->getClass()) {
            $e->setRole($class);
        }

        if (null === $e->getRole()) {
            throw new \RuntimeException('Entity must define role or class name');
        }

        if (null === $e->getMapper()) {
            $e->setMapper(null === $e->getClass() ? StdClassCycleMapper::class : BasicCycleMapper::class);
        }
    }
}
