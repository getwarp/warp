<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Bridge\SpiralPagination;

use spaceonfire\Criteria\AbstractCriteriaTest;
use spaceonfire\Criteria\CriteriaInterface;
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

        parent::testOffset();

        self::assertEquals(2, $this->criteria->getPaginator()->getPage());
        self::assertEquals(25, $this->criteria->getPaginator()->getLimit());
        self::assertEquals(25, $this->criteria->getPaginator()->getOffset());
    }

    public function testLimit(): void
    {
        self::assertEquals(1, $this->criteria->getPaginator()->getPage());
        self::assertEquals(25, $this->criteria->getPaginator()->getLimit());
        self::assertEquals(0, $this->criteria->getPaginator()->getOffset());

        parent::testLimit();

        self::assertEquals(1, $this->criteria->getPaginator()->getPage());
        self::assertEquals(25, $this->criteria->getPaginator()->getLimit());
        self::assertEquals(0, $this->criteria->getPaginator()->getOffset());
    }
}
