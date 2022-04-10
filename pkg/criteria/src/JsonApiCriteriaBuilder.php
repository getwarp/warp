<?php

declare(strict_types=1);

namespace Warp\Criteria;

/**
 * Class JsonApiCriteriaBuilder
 *
 * Provides a way to build criteria for JSON API request
 *
 * @deprecated
 * @todo replace with data grid component with more complex criteria factory
 */
final class JsonApiCriteriaBuilder
{
    private ?int $page = null;

    private ?int $pageSize = null;

    /**
     * @var int[]
     */
    private array $pageSizeRange = [10, 250];

    /**
     * @var array<string,int>|null
     */
    private ?array $orderings = null;

    /**
     * @var string[]|null
     */
    private ?array $allowedOrderByFields = null;

    /**
     * @var mixed[]
     */
    private array $include = [];

    /**
     * Set page number
     * @param int $page
     * @return $this
     */
    public function withPage(int $page): self
    {
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
        \assert(2 === \count($pageSizeRange));

        $pageSizeRange = \array_values($pageSizeRange);
        if ($pageSizeRange[0] > $pageSizeRange[1]) {
            $pageSizeRange = \array_reverse($pageSizeRange);
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
        foreach (\array_filter(\explode(',', $sort)) as $sortRule) {
            if (0 === \strpos($sortRule, '-')) {
                $sortField = \substr($sortRule, 1);
                $sortDirection = \SORT_DESC;
            } else {
                $sortField = $sortRule;
                $sortDirection = \SORT_ASC;
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
     * @param string[] $allowedOrderByFields
     * @return $this
     */
    public function withAllowedOrderByFields(array $allowedOrderByFields): self
    {
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
        if (\is_array($this->orderings) && null !== $this->allowedOrderByFields) {
            $this->orderings = \array_intersect_key($this->orderings, \array_flip($this->allowedOrderByFields));
        }

        $pageSize = \min(\max($this->pageSize, $this->pageSizeRange[0]), $this->pageSizeRange[1]);

        return Criteria::new()
            ->orderBy($this->orderings ?? [])
            ->limit($pageSize)
            ->offset($pageSize * ($this->page - 1))
            ->include($this->include);
    }
}
