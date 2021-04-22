<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Expression;

use PHPUnit\Framework\TestCase;
use spaceonfire\Common\Field\DefaultField;
use Webmozart\Expression\Constraint\EndsWith;
use Webmozart\Expression\Constraint\Same;
use Webmozart\Expression\Logic\OrX;

class SelectorTest extends TestCase
{
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
                new Selector(new DefaultField('[key]'), new Same('value')),
                true,
            ],
            [
                ['key' => 'value2'],
                new Selector(new DefaultField('[key]'), new Same('value')),
                false,
            ],
            [
                ['key2' => 'value'],
                new Selector(new DefaultField('[key]'), new Same('value')),
                false,
            ],
            [
                ['key1' => ['key2' => 'value']],
                new Selector(new DefaultField('[key1][key2]'), new Same('value')),
                true,
            ],
        ];
    }

    public function testToString(): void
    {
        $expressionA = new Selector(new DefaultField('key'), new Same('value'));
        self::assertEquals('key==="value"', $expressionA->toString());

        $expressionB = new Selector(new DefaultField('[key]'), new Same('value'));
        self::assertEquals('[key]==="value"', $expressionB->toString());

        $expressionC = new Selector(new DefaultField('[key1][key2]'), new Same('value'));
        self::assertEquals('[key1][key2]==="value"', $expressionC->toString());

        $expressionD = new Selector(new DefaultField('key'), new OrX([
            new Same('value1'),
            new Same('value2'),
        ]));
        self::assertEquals('key{==="value1" || ==="value2"}', $expressionD->toString());

        $expressionD = new Selector(new DefaultField('key'), new EndsWith('Suffix'));
        self::assertEquals('key.endsWith("Suffix")', $expressionD->toString());
    }

    public function testEquivalentTo(): void
    {
        $expressionA = new Selector(new DefaultField('keyA'), new Same('valueA'));
        $expressionADuplicate = new Selector(new DefaultField('keyA'), new Same('valueA'));
        $expressionB = new Selector(new DefaultField('keyB'), new Same('value'));
        $expressionC = new Selector(new DefaultField('keyA'), new Same('valueC'));

        self::assertTrue($expressionA->equivalentTo($expressionADuplicate));
        self::assertFalse($expressionA->equivalentTo($expressionB));
        self::assertFalse($expressionA->equivalentTo($expressionC));
    }
}
