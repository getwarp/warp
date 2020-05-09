<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Repository;

use spaceonfire\DataSource\Adapters\CycleOrm\AbstractCycleOrmTest;
use spaceonfire\DataSource\Adapters\CycleOrm\Mapper\StdClassCycleMapper;
use spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\Tag\CycleTagRepository;
use stdClass;

class CycleRepositoryWithStdClassTest extends AbstractCycleOrmTest
{
    private static $repository;

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$repository === null) {
            self::$repository = self::getRepository(CycleTagRepository::class);
        }
    }

    public function testMapper(): void
    {
        $mapper = self::$repository->getMapper();
        self::assertInstanceOf(StdClassCycleMapper::class, $mapper);
        [$entity, $data] = $mapper->init([]);
        self::assertEquals([], $data);
        self::assertInstanceOf(stdClass::class, $entity);
    }
}
