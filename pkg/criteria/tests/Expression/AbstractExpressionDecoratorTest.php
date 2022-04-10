<?php

declare(strict_types=1);

namespace Warp\Criteria\Expression;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic;

class AbstractExpressionDecoratorTest extends TestCase
{
    protected function factory(Expression $expression): AbstractExpressionDecorator
    {
        return new class($expression) extends AbstractExpressionDecorator {
        };
    }

    public function testGetInnerExpression(): void
    {
        $innerExpression = ExpressionFactory::new()->property('field', ExpressionFactory::new()->same('value'));
        $middleExpression = $this->factory($innerExpression);
        $outerExpression = $this->factory($middleExpression);

        self::assertEquals($innerExpression, $outerExpression->getInnerExpression());
        self::assertEquals($middleExpression, $outerExpression->getInnerExpression(false));
    }

    public function testEvaluate(): void
    {
        $expr = $this->factory(ExpressionFactory::new()->key('field', ExpressionFactory::new()->same('value')));

        self::assertTrue($expr->evaluate(['field' => 'value']));
        self::assertFalse($expr->evaluate(['field' => 'value2']));
        self::assertFalse($expr->evaluate(['field2' => 'value']));
    }

    public function testEquivalentTo(): void
    {
        $innerExpr = ExpressionFactory::new()->key('field', ExpressionFactory::new()->same('value'));
        $exprA = $this->factory($innerExpr);
        $exprB = $this->factory($innerExpr);

        self::assertTrue($exprA->equivalentTo($exprB));
        self::assertFalse($exprA->equivalentTo($innerExpr));
    }

    public function testToString(): void
    {
        $innerExpr = ExpressionFactory::new()->key('field', ExpressionFactory::new()->same('value'));
        $expr = $this->factory($innerExpr);
        self::assertEquals($innerExpr->toString(), $expr->toString());
    }

    public function testAndXWithInnerAndX(): void
    {
        $expr = $this->factory(ExpressionFactory::new()->andX([
            ExpressionFactory::new()->key('field1', ExpressionFactory::new()->same('value1')),
            ExpressionFactory::new()->key('field2', ExpressionFactory::new()->same('value2')),
        ]));

        $newExpr = $expr->andX(ExpressionFactory::new()->key('field3', ExpressionFactory::new()->same('value3')));

        self::assertInstanceOf(Logic\AndX::class, $newExpr);
    }

    public function testOrXWithInnerOrX(): void
    {
        $expr = $this->factory(ExpressionFactory::new()->orX([
            ExpressionFactory::new()->key('field1', ExpressionFactory::new()->same('value1')),
            ExpressionFactory::new()->key('field2', ExpressionFactory::new()->same('value2')),
        ]));

        $newExpr = $expr->orX(ExpressionFactory::new()->key('field3', ExpressionFactory::new()->same('value3')));

        self::assertInstanceOf(Logic\OrX::class, $newExpr);
    }

    public function testAndXWithEquivalent(): void
    {
        $innerExpr = ExpressionFactory::new()->key('field', ExpressionFactory::new()->same('value'));
        $exprA = $this->factory($innerExpr);
        $exprB = $this->factory($innerExpr);

        self::assertEquals($exprA, $exprA->andX($exprB));
    }

    public function testOrXWithEquivalent(): void
    {
        $innerExpr = ExpressionFactory::new()->key('field', ExpressionFactory::new()->same('value'));
        $exprA = $this->factory($innerExpr);
        $exprB = $this->factory($innerExpr);

        self::assertEquals($exprA, $exprA->orX($exprB));
    }

    /**
     * @dataProvider magicMethodProvider
     * @param string $method
     * @param array $arguments
     */
    public function testAndMagicMethods(string $method, array $arguments = []): void
    {
        $expr = $this->factory(ExpressionFactory::new()->key('field', ExpressionFactory::new()->same('value')));
        $magicMethod = 'and' . ucfirst($method);
        $newExpr = call_user_func_array([$expr, $magicMethod], $arguments);

        $expectedClass = Logic\AndX::class;
        if ($magicMethod === 'andTrue') {
            $expectedClass = get_class($expr);
        }
        if ($magicMethod === 'andFalse') {
            $expectedClass = Logic\AlwaysFalse::class;
        }

        self::assertInstanceOf($expectedClass, $newExpr);
    }

    /**
     * @dataProvider magicMethodProvider
     * @param string $method
     * @param array $arguments
     */
    public function testOrMagicMethods(string $method, array $arguments = []): void
    {
        $expr = $this->factory(ExpressionFactory::new()->key('field', ExpressionFactory::new()->same('value')));
        $magicMethod = 'or' . ucfirst($method);
        $newExpr = call_user_func_array([$expr, $magicMethod], $arguments);

        $expectedClass = Logic\OrX::class;
        if ($magicMethod === 'orTrue') {
            $expectedClass = Logic\AlwaysTrue::class;
        }
        if ($magicMethod === 'orFalse') {
            $expectedClass = get_class($expr);
        }

        self::assertInstanceOf($expectedClass, $newExpr);
    }

    public function magicMethodProvider(): array
    {
        $exprFactory = ExpressionFactory::new();
        return [
            ['all', [$exprFactory->isEmpty()]],
            ['atLeast', [1, $exprFactory->isEmpty()]],
            ['atMost', [1, $exprFactory->isEmpty()]],
            ['contains', ['substr']],
            ['count', [$exprFactory->greaterThan(2)]],
            ['endsWith', ['suffix']],
            ['equals', ['value']],
            ['exactly', [2, $exprFactory->isEmpty()]],
            ['false', []],
            ['greaterThan', [10]],
            ['greaterThanEqual', [10]],
            ['in', [[1, 2, 3]]],
            ['isEmpty', []],
            ['isInstanceOf', [\Countable::class]],
            ['key', ['key', $exprFactory->isEmpty()]],
            ['keyExists', ['key']],
            ['keyNotExists', ['key']],
            ['lessThan', [10]],
            ['lessThanEqual', [10]],
            ['matches', ['/\d+/']],
            ['method', ['someMethod', 'arg1', 'arg2', $exprFactory->isEmpty()]],
            ['not', [$exprFactory->isEmpty()]],
            ['notEmpty', []],
            ['notEquals', ['value']],
            ['notNull', []],
            ['notSame', ['value']],
            ['null', []],
            ['property', ['property', $exprFactory->isEmpty()]],
            ['same', ['value']],
            ['startsWith', ['prefix']],
            ['true', []],
        ];
    }

    /**
     * @dataProvider unknownMagicMethodProvider
     * @param string $method
     */
    public function testMagicMethodUnknown(string $method): void
    {
        $this->expectException(BadMethodCallException::class);
        $expr = $this->factory(ExpressionFactory::new()->key('field', ExpressionFactory::new()->same('value')));
        call_user_func_array([$expr, $method], []);
    }

    public function unknownMagicMethodProvider(): array
    {
        return [
            ['unknown'],
            ['andUnknown'],
            ['andAndChain'],
            ['orOrChain'],
        ];
    }
}
