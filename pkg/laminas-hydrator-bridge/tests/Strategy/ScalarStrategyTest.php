<?php

declare(strict_types=1);

namespace spaceonfire\LaminasHydratorBridge\Strategy;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use spaceonfire\Type\BuiltinType;

class ScalarStrategyTest extends TestCase
{
    public function testMain(): void
    {
        $s = new ScalarStrategy(BuiltinType::INT);

        self::assertSame(42, $s->hydrate('42', null));
        self::assertSame(42, $s->extract(42, null));

        $s = new ScalarStrategy(BuiltinType::INT, BuiltinType::STRING);

        self::assertSame(24, $s->hydrate('24', null));
        self::assertSame('24', $s->extract(24, null));
    }

    /**
     * @dataProvider hydrateDataProvider
     * @param ScalarStrategy $strategy
     * @param $value
     * @param $expected
     */
    public function testHydrate(ScalarStrategy $strategy, $value, $expected): void
    {
        self::assertSame($expected, $strategy->hydrate($value, null));
    }

    public function hydrateDataProvider(): array
    {
        return [
            [new ScalarStrategy(BuiltinType::INT), '1', 1],
            [new ScalarStrategy(BuiltinType::FLOAT), '1', 1.0],
            [new ScalarStrategy(BuiltinType::STRING), 42, '42'],
            [new ScalarStrategy(BuiltinType::BOOL), '', false],
            [new ScalarStrategy(BuiltinType::BOOL), 1, true],
        ];
    }

    public function testHydrateInvalid(): void
    {
        $s = new ScalarStrategy(BuiltinType::INT);
        $this->expectException(InvalidArgumentException::class);
        $s->hydrate('definitely not a number', null);
    }

    /**
     * @dataProvider extractDataProvider
     * @param ScalarStrategy $strategy
     * @param $value
     * @param $expected
     */
    public function testExtract(ScalarStrategy $strategy, $value, $expected): void
    {
        self::assertSame($expected, $strategy->extract($value, null));
    }

    public function testExtractInvalid(): void
    {
        $s = new ScalarStrategy(BuiltinType::INT);
        $this->expectException(InvalidArgumentException::class);
        $s->extract('definitely not a number', null);
    }

    public function extractDataProvider(): array
    {
        return [
            [new ScalarStrategy(BuiltinType::INT), 1, 1],
            [new ScalarStrategy(BuiltinType::INT, BuiltinType::STRING), 1, '1'],
            [new ScalarStrategy(BuiltinType::FLOAT, BuiltinType::INT), 1.0, 1],
            [new ScalarStrategy(BuiltinType::STRING), 42, '42'],
            [new ScalarStrategy(BuiltinType::BOOL), '', false],
            [new ScalarStrategy(BuiltinType::BOOL), 1, true],
        ];
    }

    /**
     * @dataProvider constructInvalidDataProvider
     * @param string $hydrateType
     * @param string|null $extractType
     */
    public function testConstructInvalid(string $hydrateType, ?string $extractType = null): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ScalarStrategy($hydrateType, $extractType);
    }

    public function constructInvalidDataProvider(): array
    {
        return [
            [BuiltinType::CALLABLE, null],
            [BuiltinType::INT, BuiltinType::ARRAY],
            ['invalid hydrate type', null],
            ['int', 'invalid extract type'],
        ];
    }
}
