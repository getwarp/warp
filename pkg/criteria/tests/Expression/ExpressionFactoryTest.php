<?php

declare(strict_types=1);

namespace Warp\Criteria\Expression;

use PHPUnit\Framework\TestCase;
use Warp\Common\Field\DefaultField;
use Warp\Common\Field\DefaultFieldFactory;
use Webmozart\Expression\Constraint\Same;

class ExpressionFactoryTest extends TestCase
{
    public function testSelector(): void
    {
        $ef = ExpressionFactory::new();

        $field = new DefaultField('field');
        $expr = $ef->null();

        $selector = $ef->selector($field, $expr);

        self::assertSame('field', (string)$selector->getField());
        self::assertSame($expr, $selector->getExpression());
    }

    public function testSelectorFromStringable(): void
    {
        $ef = ExpressionFactory::new();

        $field = new class {
            public function __toString()
            {
                return 'field';
            }
        };
        $expr = $ef->null();

        $selector = $ef->selector($field, $expr);

        self::assertSame('field', (string)$selector->getField());
        self::assertSame($expr, $selector->getExpression());
    }

    public function testSelectorFromInvalidField(): void
    {
        $ef = ExpressionFactory::new();

        $field = null;
        $expr = $ef->null();

        $this->expectException(\InvalidArgumentException::class);

        $ef->selector($field, $expr);
    }

    public function testKey(): void
    {
        $ef = ExpressionFactory::new();
        $expression = $ef->key('key', $innerExpr = $ef->null());
        self::assertEquals('[key]', (string)$expression->getField());
        self::assertEquals($innerExpr, $expression->getExpression());
    }

    public function testProperty(): void
    {
        $ef = ExpressionFactory::new();
        $expression = $ef->property('key.chain', $innerExpr = $ef->null());
        self::assertEquals('key.chain', (string)$expression->getField());
        self::assertEquals($innerExpr, $expression->getExpression());
    }

    public function testMagicCall(): void
    {
        $ef = ExpressionFactory::new();
        $expression = $ef->null();
        self::assertInstanceOf(Same::class, $expression);
        self::assertNull($expression->getComparedValue());
    }

    public function testMagicCallFail(): void
    {
        $ef = ExpressionFactory::new();
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Call to an undefined method Warp\Criteria\Expression\ExpressionFactory::unknown().'
        );
        $ef->unknown();
    }

    public function testFieldFactory(): void
    {
        $ef = ExpressionFactory::new();
        $ef->setFieldFactory(new DefaultFieldFactory());

        $selector = $ef->selector('field', $ef->null());

        self::assertInstanceOf(DefaultField::class, $selector->getField());
    }
}
