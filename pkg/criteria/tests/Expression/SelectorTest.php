<?php

declare(strict_types=1);

namespace Warp\Criteria\Expression;

use PHPUnit\Framework\TestCase;
use Webmozart\Expression\Constraint\EndsWith;
use Webmozart\Expression\Constraint\Same;
use Webmozart\Expression\Logic\OrX;
use Webmozart\Expression\Selector\Key;
use Webmozart\Expression\Selector\Property;

class SelectorTest extends TestCase
{
    public function testMakeFromKey(): void
    {
        $expression = Selector::makeFromKey(new Key('key', $innerExpression = new Same('value')));
        self::assertEquals('[key]', (string)$expression->getPropertyPath());
        self::assertEquals($innerExpression, $expression->getExpression());
    }

    public function testMakeFromProperty(): void
    {
        $expression = Selector::makeFromProperty(new Property('key', $innerExpression = new Same('value')));
        self::assertEquals('key', (string)$expression->getPropertyPath());
        self::assertEquals($innerExpression, $expression->getExpression());
    }

    /**
     * @dataProvider evaluateDataProvider
     * @param mixed $value
     * @param Selector $expression
     * @param bool $expected
     */
    public function testEvaluate($value, Selector $expression, bool $expected): void
    {
        self::assertEquals($expected, $expression->evaluate($value));
    }

    public function evaluateDataProvider(): array
    {
        return [
            [
                ['key' => 'value'],
                new Selector('[key]', new Same('value')),
                true,
            ],
            [
                ['key' => 'value2'],
                new Selector('[key]', new Same('value')),
                false,
            ],
            [
                ['key2' => 'value'],
                new Selector('[key]', new Same('value')),
                false,
            ],
            [
                ['key1' => ['key2' => 'value']],
                new Selector('[key1][key2]', new Same('value')),
                true,
            ],
        ];
    }

    public function testToString(): void
    {
        $expressionA = new Selector('key', new Same('value'));
        self::assertEquals('key==="value"', $expressionA->toString());

        $expressionB = new Selector('[key]', new Same('value'));
        self::assertEquals('key==="value"', $expressionB->toString());

        $expressionC = new Selector('[key1][key2]', new Same('value'));
        self::assertEquals('key1.key2==="value"', $expressionC->toString());

        $expressionD = new Selector('key', new OrX([
            new Same('value1'),
            new Same('value2'),
        ]));
        self::assertEquals('key{==="value1" || ==="value2"}', $expressionD->toString());

        $expressionD = new Selector('key', new EndsWith('Suffix'));
        self::assertEquals('key.endsWith("Suffix")', $expressionD->toString());
    }

    public function testEquivalentTo(): void
    {
        $expressionA = new Selector('keyA', new Same('valueA'));
        $expressionADuplicate = new Selector('keyA', new Same('valueA'));
        $expressionB = new Selector('keyB', new Same('value'));
        $expressionC = new Selector('keyA', new Same('valueC'));

        self::assertTrue($expressionA->equivalentTo($expressionADuplicate));
        self::assertFalse($expressionA->equivalentTo($expressionB));
        self::assertFalse($expressionA->equivalentTo($expressionC));
    }
}
