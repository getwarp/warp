<?php

declare(strict_types=1);

namespace Warp\DataSource\Bridge\CycleOrm\Repository;

use Warp\Criteria\Bridge\SpiralPagination\PaginableCriteria;
use Warp\Criteria\Criteria;
use Warp\DataSource\Bridge\CycleOrm\AbstractCycleOrmTest;
use Warp\DataSource\Exceptions\SaveException;
use Warp\DataSource\Fixtures\Domain\Post\Post;
use Warp\DataSource\Fixtures\Infrastructure\Persistence\Post\CyclePostRepository;
use Warp\DataSource\Traits\RepositoryTestTrait;

class CycleRepositoryTest extends AbstractCycleOrmTest
{
    use RepositoryTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$repository === null) {
            self::$repository = self::getRepository(CyclePostRepository::class);
        }
    }

    /**
     * @depends testRemove
     */
    public function testSaveWithoutId(): void
    {
        $entity = new Post('', 'Yet another blog post', '35a60006-c34a-4c0b-8e9d-7759f6d0c09b');
        unset($entity['id']);
        self::$repository->save($entity);
        self::assertTrue(true);
    }

    public function testFindOneNull(): void
    {
        $criteria = new Criteria();
        $criteria->where($criteria::expr()->key('title', $criteria::expr()->equals('No this post')));
        $entity = self::$repository->findOne($criteria);
        self::assertNull($entity);
    }

    /**
     * @depends testSave
     */
    public function testFindAllMatchingCriteria(): void
    {
        $criteria = new Criteria();
        $criteria->orderBy(['title' => SORT_ASC]);
        $collection = self::$repository->findAll($criteria);
        self::assertCount(3, $collection);
    }

    /**
     * @depends testSave
     */
    public function testCountMatchingCriteria(): void
    {
        $criteria = new Criteria();
        $criteria->orderBy(['title' => SORT_ASC]);
        $criteria->offset(1);
        $criteria->limit(1);
        self::assertEquals(3, self::$repository->count($criteria));
    }

    /**
     * @depends testSave
     */
    public function testFindAllPaginationSimple(): void
    {
        $criteria = new Criteria();
        $criteria->limit(1)->offset(1);
        self::assertCount(1, self::$repository->findAll($criteria));
    }

    /**
     * @depends testSave
     */
    public function testFindAllPaginationPaginator(): void
    {
        $criteria = new PaginableCriteria();
        $criteria->limit(1)->offset(1);
        self::assertCount(1, self::$repository->findAll($criteria));
    }

    public function testSaveException(): void
    {
        $this->expectException(SaveException::class);
        $entity = new Post(
            '0de0e289-28a7-4944-953a-079d09ac4865',
            'Hello, World!',
            '35a60006-c34a-4c0b-8e9d-7759f6d0c09b'
        );
        self::$repository->save($entity);
    }
}
