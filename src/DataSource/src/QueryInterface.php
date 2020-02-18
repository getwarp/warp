<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use Countable;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\DataSource\Criteria\Criteria;
use Spiral\Pagination\PaginableInterface;

interface QueryInterface extends PaginableInterface, Countable
{
    /**
     * Fetch next element
     * @return EntityInterface
     */
    public function fetch(): EntityInterface;

    /**
     * Fetch all elements
     * @return CollectionInterface
     */
    public function fetchAll(): CollectionInterface;

    /**
     * Filter query
     * @param Criteria $criteria
     * @return QueryInterface
     */
    public function matching(Criteria $criteria): QueryInterface;
}
