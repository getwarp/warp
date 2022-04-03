<?php

declare(strict_types=1);

namespace Warp\DataSource\Bridge\CycleOrm\Repository;

use Warp\DataSource\Bridge\CycleOrm\AbstractCycleOrmTest;
use Warp\DataSource\Bridge\CycleOrm\Mapper\StdClassCycleMapper;
use Warp\DataSource\Fixtures\Infrastructure\Persistence\Tag\CycleTagRepository;
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
