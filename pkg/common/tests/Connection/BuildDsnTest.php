<?php

declare(strict_types=1);

namespace Warp\Common\Connection;

use PHPUnit\Framework\TestCase;

class BuildDsnTest extends TestCase
{
    /**
     * @dataProvider buildDsnProvider
     * @param array $option
     * @param string|null $driver
     * @param string $expected
     */
    public function testBuildDsn(array $option, ?string $driver, string $expected): void
    {
        if ($driver === null) {
            self::assertSame($expected, buildDsn($option));
        } else {
            self::assertSame($expected, buildDsn($option, $driver));
        }
    }

    public function buildDsnProvider(): array
    {
        return [
            [
                [
                    'host' => 'database',
                    'port' => 3306,
                    'dbname' => 'common',
                    'charset' => 'utf8',
                ],
                'mysql',
                'mysql:host=database;port=3306;dbname=common;charset=utf8',
            ],
            [
                [
                    'charset' => 'utf8',
                    'port' => 3306,
                    'dbname' => 'common',
                    'host' => 'database',
                ],
                null,
                'mysql:host=database;port=3306;dbname=common;charset=utf8',
            ],
            [
                ['/tmp/sqlitedb.sq3'],
                'sqlite',
                'sqlite:/tmp/sqlitedb.sq3',
            ],
            [
                ['file' => ':memory:'],
                'sqlite2',
                'sqlite2::memory:',
            ],
            [
                [
                    'host' => 'database',
                    'port' => 5432,
                    'dbname' => 'common',
                    'user' => 'user',
                    'password' => 'secret',
                ],
                'pgsql',
                'pgsql:host=database;port=5432;dbname=common;user=user;password=secret',
            ],
            [
                [
                    'dbname' => 'common',
                    'port' => 5432,
                    'host' => 'database',
                ],
                'pgsql',
                'pgsql:host=database;port=5432;dbname=common',
            ],
            [
                [
                    'Server' => 'localhost',
                    'Database' => 'common',
                ],
                'sqlsrv',
                'sqlsrv:Server=localhost;Database=common',
            ],
            [
                [
                    'dbname' => '//localhost:1521/mydb',
                    'charset' => 'utf8',
                ],
                'oci',
                'oci:dbname=//localhost:1521/mydb;charset=utf8',
            ],
        ];
    }

    public function testBuildDsnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        buildDsn([], 'unknown');
    }
}
