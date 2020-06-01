<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use ArrayIterator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CollectionTypeTest extends TestCase
{
    public function testSupports(): void
    {
        self::assertTrue(CollectionType::supports('int[]'));
        self::assertTrue(CollectionType::supports('array<int>'));
        self::assertTrue(CollectionType::supports('iterable<string,int>'));
        self::assertTrue(CollectionType::supports('ArrayIterator<int>'));
        self::assertTrue(CollectionType::supports('Traversable<int>'));
        self::assertFalse(CollectionType::supports('[]'));
        self::assertFalse(CollectionType::supports('<>'));
        self::assertFalse(CollectionType::supports('ArrayIterator<>'));
        self::assertFalse(CollectionType::supports('stdClass<>'));
        self::assertFalse(CollectionType::supports('string<string>'));
    }

    public function testCreate(): void
    {
        CollectionType::create('int[]');
        CollectionType::create('array<int>');
        CollectionType::create('iterable<string,int>');
        CollectionType::create('ArrayIterator<int>');
        CollectionType::create('Traversable<int>');
        self::assertTrue(true);
    }

    public function testCreateNotSupported(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CollectionType::create('<>');
    }

    public function testCheck(): void
    {
        $integerCollection = CollectionType::create('int[]');
        self::assertTrue($integerCollection->check([1, 2, 'c' => 3]));
        self::assertFalse($integerCollection->check(['a', 'b', 'c']));

        $arrayStringInt = CollectionType::create('array<string,int>');
        self::assertTrue($arrayStringInt->check(['a' => 1, 'b' => 2, 'c' => 3]));
        self::assertFalse($arrayStringInt->check([1, 2, 'c' => 3]));
        self::assertFalse($arrayStringInt->check(new ArrayIterator()));

        $arrayIteratorInteger = CollectionType::create('ArrayIterator<int>');
        self::assertTrue($arrayIteratorInteger->check(new ArrayIterator([1, 2, 'c' => 3])));
    }

    public function testStringify(): void
    {
        self::assertEquals('int[]', (string)CollectionType::create('int[]'));
        self::assertEquals('array<string,int>', (string)CollectionType::create('array<string,int>'));
        self::assertEquals('ArrayIterator<int>', (string)CollectionType::create('ArrayIterator<int>'));
    }
}
