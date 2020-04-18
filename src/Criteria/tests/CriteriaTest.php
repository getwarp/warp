<?php

declare(strict_types=1);

namespace spaceonfire\Criteria;

class CriteriaTest extends AbstractCriteriaTest
{
    protected function createCriteria(): CriteriaInterface
    {
        return new Criteria();
    }
}
