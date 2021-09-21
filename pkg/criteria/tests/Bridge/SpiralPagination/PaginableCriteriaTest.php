<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Bridge\SpiralPagination;

use spaceonfire\Criteria\AbstractCriteriaTest;
use spaceonfire\Criteria\CriteriaInterface;
use Spiral\Pagination\PaginableInterface;
use Spiral\Pagination\Paginator;

class PaginableCriteriaTest extends AbstractCriteriaTest
{
    /**
     * @var PaginableCriteria
     */
    protected $criteria;

    protected function createCriteria(): CriteriaInterface
    {
        return new PaginableCriteria();
    }

    public function testConstructWithPaginator(): void
    {
        $paginator = (new Paginator(50))->withCount(150)->withPage(2);
        $criteria = new PaginableCriteria(null, $paginator);

        self::assertEquals(50, $criteria->getLimit());
        self::assertEquals(50, $criteria->getOffset());
        self::assertEquals($paginator, $criteria->getPaginator());
    }

    public function testOffset(): void
    {
        self::assertEquals(1, $this->criteria->getPaginator()->getPage());
        self::assertEquals(25, $this->criteria->getPaginator()->getLimit());
        self::assertEquals(0, $this->criteria->getPaginator()->getOffset());

        self::assertEquals(0, $this->criteria->getOffset());
        $this->criteria->offset(25);
        self::assertEquals(25, $this->criteria->getOffset());

        $query = $this->makeQuery(75);

        $this->criteria->paginate($query);

        self::assertEquals(2, $this->criteria->getPaginator()->getPage());
        self::assertEquals(25, $this->criteria->getPaginator()->getLimit());
        self::assertEquals(25, $this->criteria->getPaginator()->getOffset());
    }

    public function testLimit(): void
    {
        self::assertEquals(1, $this->criteria->getPaginator()->getPage());
        self::assertEquals(25, $this->criteria->getPaginator()->getLimit());
        self::assertEquals(0, $this->criteria->getPaginator()->getOffset());

        self::assertEquals(25, $this->criteria->getLimit());
        $this->criteria->limit(50);
        self::assertEquals(50, $this->criteria->getLimit());

        self::assertEquals(1, $this->criteria->getPaginator()->getPage());
        self::assertEquals(50, $this->criteria->getPaginator()->getLimit());
        self::assertEquals(0, $this->criteria->getPaginator()->getOffset());
    }

    public function testPaginate(): void
    {
        $query = $this->makeQuery(75);

        $this->criteria->paginate($query);

        self::assertSame(3, $this->criteria->getPaginator()->countPages());
    }

    private function makeQuery(int $count)
    {
        return new class($count) implements PaginableInterface, \Countable {
            public $limit = 0;
            public $offset = 0;
            public $count;

            public function __construct(int $count)
            {
                $this->count = $count;
            }

            public function count(): int
            {
                return $this->count;
            }

            public function limit(int $limit): void
            {
                $this->limit = $limit;
            }

            public function offset(int $offset): void
            {
                $this->offset = $offset;
            }
        };
    }
}
