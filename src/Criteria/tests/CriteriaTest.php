<?php

declare(strict_types=1);

namespace spaceonfire\Criteria;

use Webmozart\Expression\Expr;

class CriteriaTest extends AbstractCriteriaTest
{
    protected function createCriteria(): CriteriaInterface
    {
        return new Criteria();
    }

    public function testConstructor(): void
    {
        new Criteria(
            Expr::key('key', Expr::same('value')),
            ['key' => SORT_ASC],
            25,
            25,
            ['relationName']
        );
        self::assertTrue(true);
    }
}
