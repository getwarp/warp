<?php

declare(strict_types=1);

namespace spaceonfire\Criteria;

class CriteriaTest extends AbstractCriteriaTest
{
    protected function createCriteria(): CriteriaInterface
    {
        return new Criteria();
    }

    public function testConstructor(): void
    {
        new Criteria(
            Criteria::expr()->key('key', Criteria::expr()->same('value')),
            ['key' => SORT_ASC],
            25,
            25,
            ['relationName']
        );
        self::assertTrue(true);
    }
}
