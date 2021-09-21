<?php

declare(strict_types=1);

namespace spaceonfire\Criteria;

interface FilterableInterface
{
    /**
     * @param CriteriaInterface $criteria
     * @return mixed
     */
    public function matching(CriteriaInterface $criteria);
}
