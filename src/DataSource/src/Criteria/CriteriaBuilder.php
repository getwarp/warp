<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Criteria;

use Spiral\Pagination\Paginator;
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
     * @var array|null
     */
    protected $orderings;
    /**
     * @var string[]
     */
    protected $include;

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
                $sortDirection = Criteria::DESC;
            } else {
                $sortField = $sortRule;
                $sortDirection = Criteria::ASC;
            }

            $orderings[$sortField] = $sortDirection;
        }

        if (!empty($orderings)) {
            $builder->orderings = $orderings;
        }

        return $builder;
    }

    public function withInclude(array $include): self
    {
        Assert::allStringNotEmpty($include);
        $builder = clone $this;
        $builder->include = $include;
        return $builder;
    }

    public function withFilter(string $filter): self
    {
        $builder = clone $this;

        // TODO: implement filter string to Expression converter

        return $builder;
    }

    public function build(): Criteria
    {
        $criteria = Criteria::create();

        $criteria->setPaginator($this->buildPaginator());

        if ($this->orderings) {
            // TODO: filter order fields in allowed range in build stage
            $criteria->orderBy($this->orderings);
        }

        $criteria->setInclude($this->include);

        return $criteria;
    }

    protected function buildPaginator(): Paginator
    {
        $pageSize = $this->pageSize;
        if ($pageSize < $this->pageSizeRange[0]) {
            $pageSize = $this->pageSizeRange[0];
        }
        if ($pageSize > $this->pageSizeRange[1]) {
            $pageSize = $this->pageSizeRange[1];
        }

        $paginator = new Paginator($pageSize);

        if ($this->page > 1) {
            $paginator = $paginator->withPage($this->page);
        }

        return $paginator;
    }
}
