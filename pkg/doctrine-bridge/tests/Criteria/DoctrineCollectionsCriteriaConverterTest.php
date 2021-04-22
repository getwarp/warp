<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Doctrine\Criteria;

use Doctrine\Common\Collections\Criteria as DoctrineCriteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use spaceonfire\Criteria\Expression\ExpressionFactory;

class DoctrineCollectionsCriteriaConverterTest extends TestCase
{
    public function testConvert(): void
    {
        $expressionFactory = ExpressionFactory::new();
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

        $expectedExpression = $expressionFactory->andX([
            $expressionFactory->orX([
                $expressionFactory->property('key', $expressionFactory->same('value')),
                $expressionFactory->property('key', $expressionFactory->greaterThan(10)),
                $expressionFactory->property('key', $expressionFactory->greaterThanEqual(10)),
                $expressionFactory->property('key', $expressionFactory->lessThan(20)),
                $expressionFactory->property('key', $expressionFactory->lessThanEqual(20)),
            ]),
            $expressionFactory->property('key2', $expressionFactory->notSame('value2')),
            $expressionFactory->property('key2', $expressionFactory->null()),
            $expressionFactory->property('key3', $expressionFactory->in([1, 2, 3])),
            $expressionFactory->property('key3', $expressionFactory->not($expressionFactory->in([1, 2, 3]))),
            $expressionFactory->property('key4', $expressionFactory->contains('substring')),
            $expressionFactory->property('key4', $expressionFactory->startsWith('substring')),
            $expressionFactory->property('key4', $expressionFactory->endsWith('substring')),
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
