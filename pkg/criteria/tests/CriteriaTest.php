<?php

declare(strict_types=1);

namespace spaceonfire\Criteria;

use PHPUnit\Framework\TestCase;
use spaceonfire\Criteria\Expression\ExpressionFactory;
use Webmozart\Expression\Logic\AndX;
use Webmozart\Expression\Logic\OrX;

class CriteriaTest extends TestCase
{
    protected function makeCriteria(): CriteriaInterface
    {
        return Criteria::new();
    }

    public function testConstructor(): void
    {
        Criteria::new(
            ExpressionFactory::new()->key('key', ExpressionFactory::new()->same('value')),
            ['key' => SORT_ASC],
            25,
            25,
            ['relationName']
        );
        self::assertTrue(true);
    }

    public function testOrderBy(): void
    {
        $criteria = $this->makeCriteria();
        $criteria = $criteria->orderBy(['key1' => SORT_DESC, 'key2' => SORT_ASC]);
        self::assertEquals(['key1' => SORT_DESC, 'key2' => SORT_ASC], $criteria->getOrderBy());
    }

    public function testOrderByInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makeCriteria()->orderBy(['key1' => 0, 'key2' => 'asc']);
    }

    public function testOffset(): void
    {
        $criteria = $this->makeCriteria();
        self::assertEquals(0, $criteria->getOffset());

        $criteria = $criteria->offset(25);
        self::assertEquals(25, $criteria->getOffset());
    }

    public function testInclude(): void
    {
        $criteria = $this->makeCriteria();
        $criteria = $criteria->include(['relName', 'chained.relName']);
        self::assertEquals(['relName', 'chained.relName'], $criteria->getInclude());
    }

    public function testLimit(): void
    {
        $criteria = $this->makeCriteria();
        self::assertEquals(null, $criteria->getLimit());

        $criteria = $criteria->limit(25);
        self::assertEquals(25, $criteria->getLimit());
    }

    public function testWhere(): void
    {
        $criteria = $this->makeCriteria();
        self::assertEquals(null, $criteria->getWhere());

        $expression = ExpressionFactory::new()->property('fieldName', ExpressionFactory::new()->equals('test'));
        $criteria = $criteria->where($expression);
        self::assertEquals($expression, $criteria->getWhere());

        $criteria = $criteria->where(null);
        self::assertEquals(null, $criteria->getWhere());
    }

    public function testAndWhere(): void
    {
        $criteria = $this->makeCriteria();
        self::assertEquals(null, $criteria->getWhere());

        $expressionA = ExpressionFactory::new()->property('field1', ExpressionFactory::new()->equals('test'));
        $criteria = $criteria->andWhere($expressionA);
        self::assertEquals($expressionA, $criteria->getWhere());

        $expressionB = ExpressionFactory::new()->property('field2', ExpressionFactory::new()->greaterThan(10));
        $criteria = $criteria->andWhere($expressionB);

        self::assertInstanceOf(AndX::class, $criteria->getWhere());
        self::assertTrue($criteria->getWhere()->equivalentTo(new AndX([$expressionA, $expressionB])));
    }

    public function testOrWhere(): void
    {
        $criteria = $this->makeCriteria();
        self::assertEquals(null, $criteria->getWhere());
        $expressionA = ExpressionFactory::new()->property('field1', ExpressionFactory::new()->equals('test'));
        $criteria = $criteria->orWhere($expressionA);
        self::assertEquals($expressionA, $criteria->getWhere());

        $expressionB = ExpressionFactory::new()->property('field2', ExpressionFactory::new()->greaterThan(10));
        $criteria = $criteria->orWhere($expressionB);

        self::assertInstanceOf(OrX::class, $criteria->getWhere());
        self::assertTrue($criteria->getWhere()->equivalentTo(new OrX([$expressionA, $expressionB])));
    }

    public function testMergeOverrideAll(): void
    {
        $criteria = $this->makeCriteria();

        $expression = ExpressionFactory::new()->property('fieldName', ExpressionFactory::new()->equals('test'));

        $newCriteria = $this->makeCriteria()
            ->limit(50)
            ->offset(20)
            ->include(['includeA'])
            ->orderBy(['orderAsc' => SORT_ASC])
            ->where($expression);

        $mergedCriteria = $criteria->merge($newCriteria);

        self::assertEquals(50, $mergedCriteria->getLimit());
        self::assertEquals(20, $mergedCriteria->getOffset());
        self::assertEquals(['includeA'], $mergedCriteria->getInclude());
        self::assertEquals(['orderAsc' => SORT_ASC], $mergedCriteria->getOrderBy());
        self::assertEquals($expression, $mergedCriteria->getWhere());
    }

    public function testMergeOverridePartially(): void
    {
        $criteria = $this->makeCriteria();
        $criteria = $criteria->offset(50);

        $newCriteria = $this->makeCriteria()->offset(20);

        $mergedCriteria = $criteria->merge($newCriteria);

        self::assertEquals($criteria->getLimit(), $mergedCriteria->getLimit());
        self::assertEquals(20, $mergedCriteria->getOffset());
        self::assertEquals($criteria->getInclude(), $mergedCriteria->getInclude());
        self::assertEquals($criteria->getOrderBy(), $mergedCriteria->getOrderBy());
        self::assertEquals($criteria->getWhere(), $mergedCriteria->getWhere());
    }
}
