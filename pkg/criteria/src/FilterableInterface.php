<?php

declare(strict_types=1);

namespace spaceonfire\Criteria;

/**
 * @todo maybe add generic template for matching() return.
 */
interface FilterableInterface
{
    /**
     * @param CriteriaInterface $criteria
     * @return mixed
     */
    public function matching(CriteriaInterface $criteria);
}
