<?php

declare(strict_types=1);

namespace spaceonfire\Container\Argument;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use spaceonfire\Container\RawValueHolder;
use stdClass;

class ArgumentValueTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     * @param mixed $val
     */
    public function testGetValue($val): void
    {
        self::assertSame($val, (new RawValueHolder($val))->getValue());
    }

    public function dataProvider(): array
    {
        return [
            [42],
            ['foo'],
            [[]],
            [new stdClass()],
            [new ArrayIterator([1, 2, 3])],
            [false],
        ];
    }
}
