<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use Countable;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\Criteria\FilterableInterface;
use Spiral\Pagination\PaginableInterface;

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
