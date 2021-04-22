<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

use PHPUnit\Framework\TestCase;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\TypeInterface;

class ScalarCasterTest extends TestCase
{
    public function validInputProvider(): array
    {
        return [
            [BuiltinType::int(), 1, 1],
            [BuiltinType::int(), '1', 1],
            [BuiltinType::int(), '  1', 1],
            [BuiltinType::float(), 1, 1.0],
            [BuiltinType::float(), '1', 1.0],
            [BuiltinType::float(), 1.1, 1.1],
            [BuiltinType::float(), '1.1', 1.1],
            [BuiltinType::float(), '  1.1  ', 1.1],
            [BuiltinType::string(), 'string', 'string'],
            [BuiltinType::string(), $this->makeStringable('string'), 'string'],
            [BuiltinType::string(), 1.1, '1.1'],
            [BuiltinType::string(), 1, '1'],
            [BuiltinType::string(), null, ''],
            [BuiltinType::bool(), true, true],
            [BuiltinType::bool(), false, false],
            [BuiltinType::bool(), 1, true],
            [BuiltinType::bool(), 0, false],
            [BuiltinType::bool(), 'yes', true],
            [BuiltinType::bool(), 'no', false],
            [BuiltinType::bool(), 'on', true],
            [BuiltinType::bool(), 'off', false],
        ];
    }

    public function invalidInputProvider(): array
    {
        return [
            [BuiltinType::int(), 1.1],
            [BuiltinType::int(), '1.1'],
            [BuiltinType::string(), []],
            [BuiltinType::string(), (object)[]],
            [BuiltinType::bool(), 10],
            [BuiltinType::bool(), []],
        ];
    }

    /**
     * @dataProvider validInputProvider
     * @param TypeInterface $type
     * @param $input
     */
    public function testAccept(TypeInterface $type, $input): void
    {
        $caster = new ScalarCaster($type);

        self::assertTrue($caster->accepts($input));
    }

    /**
     * @dataProvider invalidInputProvider
     * @param TypeInterface $type
     * @param $input
     */
    public function testNotAccept(TypeInterface $type, $input): void
    {
        $caster = new ScalarCaster($type);

        self::assertFalse($caster->accepts($input));
    }

    /**
     * @dataProvider validInputProvider
     * @param TypeInterface $type
     * @param $input
     * @param $expected
     */
    public function testCast(TypeInterface $type, $input, $expected): void
    {
        $caster = new ScalarCaster($type);

        self::assertSame($expected, $caster->cast($input));
    }

    /**
     * @dataProvider invalidInputProvider
     * @param TypeInterface $type
     * @param $input
     */
    public function testCastInvalid(TypeInterface $type, $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $caster = new ScalarCaster($type);
        $caster->cast($input);
    }

    public function testConstructWithNonScalarType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ScalarCaster(BuiltinType::array());
    }

    private function makeStringable(string $value): object
    {
        return new class($value) {
            private string $value;

            public function __construct(string $value)
            {
                $this->value = $value;
            }

            public function __toString(): string
            {
                return $this->value;
            }
        };
    }
}
