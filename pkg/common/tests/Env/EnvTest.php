<?php

declare(strict_types=1);

namespace spaceonfire\Common\Env;

use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    /**
     * @dataProvider envArgumentsDataProvider
     * @param string $name
     * @param $default
     * @param $expected
     */
    public function testEnv(string $name, $default, $expected): void
    {
        self::assertSame($expected, env($name, $default));
    }

    public function envArgumentsDataProvider(): array
    {
        return [
            ['FOO', null, 'BAR'],
            ['BAZ', null, null],
            [
                'BAZ',
                function () {
                    return 'baz';
                },
                'baz',
            ],
            ['TRUE', null, true],
            ['FALSE', null, false],
            ['NULL', null, null],
            ['ESCAPED_NULL_1', null, 'null'],
            ['ESCAPED_NULL_2', null, 'null'],
            ['ESCAPED_NULL_3', null, 'x"null"x'],
        ];
    }
}
