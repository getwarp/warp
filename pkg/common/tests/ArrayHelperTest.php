<?php

declare(strict_types=1);

namespace spaceonfire\Common;

use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
    public function testIsArrayAssoc(): void
    {
        self::assertFalse(ArrayHelper::isArrayAssoc('test'));
        self::assertFalse(ArrayHelper::isArrayAssoc([]));
        self::assertFalse(ArrayHelper::isArrayAssoc([1, 2, 3]));
        self::assertFalse(ArrayHelper::isArrayAssoc([1]));
        self::assertTrue(ArrayHelper::isArrayAssoc(['name' => 1, 'value' => 'test']));
        self::assertTrue(ArrayHelper::isArrayAssoc(['name' => 1, 'value' => 'test', 3]));
    }

    public function testFlatten(): void
    {
        $unflatten = [
            'name' => 'Foobar',
            'date' => '2021-09-18',
            'admin' => [
                'name' => [
                    'first' => 'John',
                    'last' => 'Doe',
                ],
            ],
            'tags' => ['one', 'two', 'three'],
        ];

        $flatten = [
            'name' => 'Foobar',
            'date' => '2021-09-18',
            'admin.name.first' => 'John',
            'admin.name.last' => 'Doe',
            'tags' => ['one', 'two', 'three'],
        ];

        self::assertEquals($flatten, ArrayHelper::flatten($unflatten));
    }

    public function testFlattenEmpty(): void
    {
        self::assertSame([], ArrayHelper::flatten([]));
    }

    public function testFlattenSimple(): void
    {
        $array = [
            'name' => 'Foobar',
            'date' => '2021-09-18',
            'tags' => ['one', 'two', 'three'],
        ];

        self::assertSame($array, ArrayHelper::flatten($array));
    }

    public function testUnflatten(): void
    {
        $unflatten = [
            'name' => 'Foobar',
            'date' => '2021-09-18',
            'admin' => [
                'name' => [
                    'first' => 'John',
                    'last' => 'Doe',
                ],
            ],
            'tags' => ['one', 'two', 'three'],
        ];

        $flatten = [
            'name' => 'Foobar',
            'date' => '2021-09-18',
            'admin.name.first' => 'John',
            'admin.name.last' => 'Doe',
            'tags' => ['one', 'two', 'three'],
        ];

        self::assertEquals($unflatten, ArrayHelper::unflatten($flatten));
    }

    public function testUnflattenEmpty(): void
    {
        self::assertSame([], ArrayHelper::unflatten([]));
    }

    public function testUnflattenSimple(): void
    {
        $array = [
            'name' => 'Foobar',
            'date' => '2021-09-18',
            'tags' => ['one', 'two', 'three'],
        ];

        self::assertSame($array, ArrayHelper::unflatten($array));
    }
}
