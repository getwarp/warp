<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

use PHPUnit\Framework\TestCase;

class NullCasterTest extends TestCase
{
    public function inputProvider(): array
    {
        return [
            [1],
            ['1'],
            [null],
            [[]],
            [(object)[]],
        ];
    }

    /**
     * @dataProvider inputProvider
     * @param $input
     */
    public function testAccepts($input): void
    {
        $caster = new NullCaster();

        self::assertTrue($caster->accepts($input));
    }

    /**
     * @dataProvider inputProvider
     * @param $input
     */
    public function testCast($input): void
    {
        $caster = new NullCaster();

        self::assertNull($caster->cast($input));
    }
}
