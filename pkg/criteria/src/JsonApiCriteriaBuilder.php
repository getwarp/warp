<?php

declare(strict_types=1);

namespace spaceonfire\Criteria;

use Webmozart\Assert\Assert;

/**
 * Class JsonApiCriteriaBuilder
 *
 * Provides a way to build criteria for JSON API request
 *
 * TODO: support parsing filter
 */
class JsonApiCriteriaBuilder
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
     * @var array|null
     */
    protected $allowedOrderByFields;

    /**
     * @var mixed[]
     */
    protected $include = [];

    /**
     * Set page number
     * @param int $page
     * @return $this
     */
    public function withPage(int $page): self
    {
        Assert::natural($page);
        $builder = clone $this;
        $builder->page = $page;
        return $builder;
    }

    /**
     * Set page size
     * @param int $pageSize
     * @return $this
     */
    public function withPageSize(int $pageSize): self
    {
        Assert::natural($pageSize);
        $builder = clone $this;
        $builder->pageSize = $pageSize;
        return $builder;
    }

    /**
     * Set allowed page size range
     * @param int[] $pageSizeRange
     * @return $this
     */
    public function withPageSizeRange(array $pageSizeRange): self
    {
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

    /**
     * Set sort fields
     * @param string $sort
     * @return $this
     */
    public function withSort(string $sort): self
    {
        $builder = clone $this;

        $orderings = [];
        foreach (array_filter(explode(',', $sort)) as $sortRule) {
            if (0 === strpos($sortRule, '-')) {
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
     * Set fields that allowed to use for sorting
     * @param array $allowedOrderByFields
     * @return $this
     */
    public function withAllowedOrderByFields(array $allowedOrderByFields): self
    {
        Assert::allString($allowedOrderByFields);
        $builder = clone $this;
        $builder->allowedOrderByFields = $allowedOrderByFields;
        return $builder;
    }

    /**
     * Set include relations
     * @param mixed[] $include
     * @return $this
     */
    public function withInclude(array $include): self
    {
        $builder = clone $this;
        $builder->include = $include;
        return $builder;
    }

    /**
     * Build criteria
     * @return CriteriaInterface
     */
    public function build(): CriteriaInterface
    {
        $criteria = new Criteria();

        if (is_array($this->orderings)) {
            if (null !== $this->allowedOrderByFields) {
                $this->orderings = array_intersect_key($this->orderings, array_flip($this->allowedOrderByFields));
            }

            $criteria->orderBy($this->orderings);
        }

        $pageSize = min(max($this->pageSize, $this->pageSizeRange[0]), $this->pageSizeRange[1]);
        $criteria->limit($pageSize)->offset($pageSize * ($this->page - 1));

        $criteria->include($this->include);

        return $criteria;
    }
}
