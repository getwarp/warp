<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Bridge\DoctrineCollections;

use Doctrine\Common\Collections\Criteria as DoctrineCriteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use spaceonfire\Criteria\Criteria;

class DoctrineCollectionsCriteriaConverterTest extends TestCase
{
    public function testConvert(): void
    {
        $expressionBuilder = Criteria::expr();
        $doctrineExpressionBuilder = DoctrineCriteria::expr();

        $doctrineExpression = $doctrineExpressionBuilder->andX(
            $doctrineExpressionBuilder->orX(
                $doctrineExpressionBuilder->eq('key', 'value'),
                $doctrineExpressionBuilder->gt('key', 10),
                $doctrineExpressionBuilder->gte('key', 10),
                $doctrineExpressionBuilder->lt('key', 20),
                $doctrineExpressionBuilder->lte('key', 20)
            ),
            $doctrineExpressionBuilder->neq('key2', 'value2'),
            $doctrineExpressionBuilder->isNull('key2'),
            $doctrineExpressionBuilder->in('key3', [1, 2, 3]),
            $doctrineExpressionBuilder->notIn('key3', [1, 2, 3]),
            $doctrineExpressionBuilder->contains('key4', 'substring'),
            $doctrineExpressionBuilder->startsWith('key4', 'substring'),
            $doctrineExpressionBuilder->endsWith('key4', 'substring')
        );

        $expectedExpression = $expressionBuilder->andX([
            $expressionBuilder->orX([
                $expressionBuilder->property('key', $expressionBuilder->same('value')),
                $expressionBuilder->property('key', $expressionBuilder->greaterThan(10)),
                $expressionBuilder->property('key', $expressionBuilder->greaterThanEqual(10)),
                $expressionBuilder->property('key', $expressionBuilder->lessThan(20)),
                $expressionBuilder->property('key', $expressionBuilder->lessThanEqual(20)),
            ]),
            $expressionBuilder->property('key2', $expressionBuilder->notSame('value2')),
            $expressionBuilder->property('key2', $expressionBuilder->null()),
            $expressionBuilder->property('key3', $expressionBuilder->in([1, 2, 3])),
            $expressionBuilder->property('key3', $expressionBuilder->not($expressionBuilder->in([1, 2, 3]))),
            $expressionBuilder->property('key4', $expressionBuilder->contains('substring')),
            $expressionBuilder->property('key4', $expressionBuilder->startsWith('substring')),
            $expressionBuilder->property('key4', $expressionBuilder->endsWith('substring')),
        ]);

        $doctrineCriteria = new DoctrineCriteria(
            $doctrineExpression,
            ['key1' => DoctrineCriteria::ASC, 'key2' => DoctrineCriteria::DESC],
            50,
            25
        );

        $criteria = (new DoctrineCollectionsCriteriaConverter())->convert($doctrineCriteria);

        self::assertTrue($criteria->getWhere()->equivalentTo($expectedExpression));
        self::assertEquals(['key1' => SORT_ASC, 'key2' => SORT_DESC], $criteria->getOrderBy());
        self::assertEquals(50, $criteria->getOffset());
        self::assertEquals(25, $criteria->getLimit());
    }

    public function testConvertMemberOfNotSupported(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $doctrineExpressionBuilder = DoctrineCriteria::expr();
        $doctrineCriteria = new DoctrineCriteria($doctrineExpressionBuilder->memberOf('key', 'value'));
        (new DoctrineCollectionsCriteriaConverter())->convert($doctrineCriteria);
    }

    public function testConvertUnknownCompositeExpression(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $doctrineExpressionBuilder = DoctrineCriteria::expr();
        $doctrineCriteria = new DoctrineCriteria(new CompositeExpression('UNKNOWN', [
            $doctrineExpressionBuilder->eq('key', 'value'),
            $doctrineExpressionBuilder->neq('key2', 'value2'),
        ]));
        (new DoctrineCollectionsCriteriaConverter())->convert($doctrineCriteria);
    }
}
