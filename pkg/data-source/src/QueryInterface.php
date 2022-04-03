<?php

declare(strict_types=1);

namespace Warp\DataSource;

use Countable;
use Spiral\Pagination\PaginableInterface;
use Warp\Collection\CollectionInterface;
use Warp\Criteria\CriteriaInterface;
use Warp\Criteria\FilterableInterface;

interface QueryInterface extends Countable, PaginableInterface, FilterableInterface
{
    /**
     * Fetch one element.
     * @return EntityInterface|null
     */
    public function fetchOne(): ?EntityInterface;

    /**
     * Fetch all elements.
     * @return CollectionInterface
     */
    public function fetchAll(): CollectionInterface;

    /**
     * Filter query with given criteria.
     * @param CriteriaInterface $criteria
     * @return QueryInterface
     */
    public function matching(CriteriaInterface $criteria): self;
}
