<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Adapter\SpiralPagination;

use spaceonfire\Criteria\CriteriaInterface;
use Spiral\Pagination\Paginator;
use Spiral\Pagination\PaginatorInterface;
use Webmozart\Assert\Assert;

class CriteriaBuilder
{
    /**
     * @var int|null
     */
    protected $page;
    /**
     * @var int|null
     */
    protected $pageSize;
    /**
     * @var int[]
     */
    protected $pageSizeRange = [10, 250];
    /**
     * @var array<string,int>|null
     */
    protected $orderings;
    /**
     * @var mixed[]
     */
    protected $include = [];

    public function withPage(int $page): self
    {
        Assert::natural($page);
        $builder = clone $this;
        $builder->page = $page;
        return $builder;
    }

    public function withPageSize(int $pageSize): self
    {
        Assert::natural($pageSize);
        $builder = clone $this;
        $builder->pageSize = $pageSize;
        return $builder;
    }

    /**
     * @param int[] $pageSizeRange
     * @return $this
     */
    public function withPageSizeRange(array $pageSizeRange): self
    {
        Assert::allInteger($pageSizeRange);
        Assert::allNatural($pageSizeRange);
        Assert::count($pageSizeRange, 2);

        $pageSizeRange = array_values($pageSizeRange);
        if ($pageSizeRange[0] > $pageSizeRange[1]) {
            $pageSizeRange = array_reverse($pageSizeRange);
        }

        $builder = clone $this;
        $builder->pageSizeRange = $pageSizeRange;
        return $builder;
    }

    public function withSort(string $sort): self
    {
        $builder = clone $this;

        $orderings = [];
        foreach (array_filter(explode(',', $sort)) as $sortRule) {
            if (strpos($sortRule, '-') === 0) {
                $sortField = substr($sortRule, 1);
                $sortDirection = SORT_DESC;
            } else {
                $sortField = $sortRule;
                $sortDirection = SORT_ASC;
            }

            $orderings[$sortField] = $sortDirection;
        }

        if (!empty($orderings)) {
            $builder->orderings = $orderings;
        }

        return $builder;
    }

    /**
     * @param mixed[] $include
     * @return $this
     */
    public function withInclude(array $include): self
    {
        $builder = clone $this;
        $builder->include = $include;
        return $builder;
    }

    private function buildPaginator(): PaginatorInterface
    {
        /** @var int $pageSize */
        $pageSize = $this->pageSize;
        if ($pageSize < $this->pageSizeRange[0]) {
            $pageSize = $this->pageSizeRange[0];
        }
        if ($pageSize > $this->pageSizeRange[1]) {
            $pageSize = $this->pageSizeRange[1];
        }

        $paginator = new Paginator($pageSize);

        if ($this->page !== null && $this->page > 1) {
            $paginator = $paginator->withPage($this->page)->withCount($this->page * $pageSize);
        }

        return $paginator;
    }

    public function build(): CriteriaInterface
    {
        $criteria = new PaginableCriteria();

        $this->buildPaginator()->paginate($criteria);

        if (is_array($this->orderings)) {
            // TODO: filter order fields in allowed range in build stage
            $criteria->orderBy($this->orderings);
        }

        $criteria->include($this->include);

        return $criteria;
    }
}
