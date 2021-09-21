<?php

declare(strict_types=1);

namespace spaceonfire\LaminasHydratorBridge\Strategy;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BooleanStrategyTest extends TestCase
{
    /**
     * @dataProvider extractDataProvider
     * @param BooleanStrategy $strategy
     * @param mixed $input
     * @param mixed $output
     */
    public function testExtract(BooleanStrategy $strategy, $input, $output): void
    {
        self::assertSame($output, $strategy->extract($input));
    }

    public function extractDataProvider(): array
    {
        return [
            [new BooleanStrategy(1, 0), true, 1],
            [new BooleanStrategy(1, 0), false, 0],
            [new BooleanStrategy('Y', 'N'), true, 'Y'],
            [new BooleanStrategy('Y', 'N'), false, 'N'],
            [new BooleanStrategy([1, 'Y'], [0, 'N']), true, 1],
            [new BooleanStrategy([1, 'Y'], [0, 'N']), false, 0],
            [new BooleanStrategy(['Y', 1], ['N', 0]), true, 'Y'],
            [new BooleanStrategy(['Y', 1], ['N', 0]), false, 'N'],
        ];
    }

    public function testExtractInvalid(): void
    {
        $strategy = new BooleanStrategy(1, 0);
        $this->expectException(InvalidArgumentException::class);
        $strategy->extract(1);
    }

    /**
     * @dataProvider hydrateDataProvider
     * @param BooleanStrategy $strategy
     * @param mixed $input
     * @param mixed $output
     */
    public function testHydrate(BooleanStrategy $strategy, $input, $output): void
    {
        self::assertSame($output, $strategy->hydrate($input, null));
    }

    public function hydrateDataProvider(): array
    {
        return [
            [new BooleanStrategy(1, 0), 1, true],
            [new BooleanStrategy(1, 0), 0, false],
            [new BooleanStrategy(1, 0), -1, false],
            [new BooleanStrategy(1, 0), '1', false],
            [new BooleanStrategy(1, 0, false), '1', true],
            [new BooleanStrategy('Y', 'N'), 'Y', true],
            [new BooleanStrategy('Y', 'N'), 'N', false],
            [new BooleanStrategy('Y', 'N'), 'some other value', false],
            [new BooleanStrategy('Y', 'N'), true, true],
            [new BooleanStrategy('Y', 'N'), null, false],
            [new BooleanStrategy([1, 'Y'], [0, 'N']), 1, true],
            [new BooleanStrategy([1, 'Y'], [0, 'N']), 0, false],
            [new BooleanStrategy([1, 'Y'], [0, 'N']), 'Y', true],
            [new BooleanStrategy([1, 'Y'], [0, 'N']), 'N', false],
            [new BooleanStrategy([1, 'Y'], [0, 'N'], false), '1', true],
            [new BooleanStrategy([1, 'Y'], [0, 'N'], false), '0', false],
        ];
    }

    public function testConstruct(): void
    {
        new BooleanStrategy('Y', 'N', false);
        self::assertTrue(true);
    }

    /**
     * @dataProvider constructInvalidProvider
     * @param mixed $trueValue
     * @param mixed $falseValue
     * @param bool $strict
     */
    public function testConstructInvalid($trueValue, $falseValue, bool $strict = true): void
    {
        $this->expectException(InvalidArgumentException::class);
        new BooleanStrategy($trueValue, $falseValue, $strict);
    }

    public function constructInvalidProvider(): array
    {
        return [
            [null, false],
            [1, null],
            [[], 0],
            [1, []],
            [[null], 0],
            [1, [null]],
        ];
    }
}
