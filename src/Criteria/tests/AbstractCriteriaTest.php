<?php

declare(strict_types=1);

namespace spaceonfire\Criteria;

use PHPUnit\Framework\TestCase;
use Webmozart\Expression\Expr;

abstract class AbstractCriteriaTest extends TestCase
{
    protected $criteria;

    public function testOrderBy(): void
    {
        $this->criteria->orderBy(['key1' => SORT_DESC, 'key2' => SORT_ASC]);
        self::assertEquals(['key1' => SORT_DESC, 'key2' => SORT_ASC], $this->criteria->getOrderBy());
    }

    public function testOrderByInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->criteria->orderBy(['key1' => 0, 'key2' => 'asc']);
    }

    public function testOffset(): void
    {
        self::assertEquals(0, $this->criteria->getOffset());
        $this->criteria->offset(25);
        self::assertEquals(25, $this->criteria->getOffset());
    }

    public function testInclude(): void
    {
        $this->criteria->include(['relName', 'chained.relName']);
        self::assertEquals(['relName', 'chained.relName'], $this->criteria->getInclude());
    }

    public function testLimit(): void
    {
        self::assertEquals(null, $this->criteria->getLimit());
        $this->criteria->limit(25);
        self::assertEquals(25, $this->criteria->getLimit());
    }

    public function testWhere(): void
    {
        self::assertEquals(null, $this->criteria->getWhere());
        $expression = Expr::property('fieldName', Expr::equals('test'));
        $this->criteria->where($expression);
        self::assertEquals($expression, $this->criteria->getWhere());
    }
}
