<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Adapter\Pagination;

use spaceonfire\Criteria\AbstractCriteriaTest;
use spaceonfire\Criteria\Criteria;

class PaginableCriteriaTest extends AbstractCriteriaTest
{
    /**
     * @var PaginableCriteria
     */
    protected $criteria;

    protected function setUp(): void
    {
        $this->criteria = new PaginableCriteria();
    }

    public function testExport(): void
    {
        $originalCriteria = new Criteria();
        $paginableCriteria = new PaginableCriteria($originalCriteria);
        self::assertEquals($originalCriteria, $paginableCriteria->export());
    }

    public function testMakePaginator(): void
    {
        $this->criteria->limit(50)->offset(100);

        $paginator = $this->criteria->makePaginator()->withCount(150);
        self::assertEquals(50, $paginator->getLimit());
        self::assertEquals(3, $paginator->getPage());
    }
}
