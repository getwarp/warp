<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Expression;

use PHPUnit\Framework\TestCase;
use Webmozart\Expression\Constraint\Same;

class ExpressionBuilderTest extends TestCase
{
    private $builder;

    protected function setUp(): void
    {
        $this->builder = new ExpressionBuilder();
    }

    public function testKey(): void
    {
        $expression = $this->builder->key('key', $innerExpr = $this->builder->null());
        self::assertEquals('[key]', (string)$expression->getPropertyPath());
        self::assertEquals($innerExpr, $expression->getExpression());
    }

    public function testProperty(): void
    {
        $expression = $this->builder->property('key.chain', $innerExpr = $this->builder->null());
        self::assertEquals('key.chain', (string)$expression->getPropertyPath());
        self::assertEquals($innerExpr, $expression->getExpression());
    }

    public function testMagicCall(): void
    {
        $expression = $this->builder->null();
        self::assertInstanceOf(Same::class, $expression);
        self::assertNull($expression->getComparedValue());
    }

    public function testMagicCallFail(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Call to undefined method spaceonfire\Criteria\Expression\ExpressionBuilder::unknown()'
        );
        $this->builder->unknown();
    }
}
