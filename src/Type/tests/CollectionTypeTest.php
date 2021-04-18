<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use ArrayIterator;
use PHPUnit\Framework\TestCase;

class CollectionTypeTest extends TestCase
{
    public function testCheck(): void
    {
        $integerCollection = new CollectionType(new BuiltinType(BuiltinType::INT));
        self::assertTrue($integerCollection->check([1, 2, 'c' => 3]));
        self::assertFalse($integerCollection->check(['a', 'b', 'c']));

        $arrayStringInt = new CollectionType(
            new BuiltinType(BuiltinType::INT),
            new BuiltinType(BuiltinType::STRING),
            new BuiltinType(BuiltinType::ARRAY)
        );
        self::assertTrue($arrayStringInt->check(['a' => 1, 'b' => 2, 'c' => 3]));
        self::assertFalse($arrayStringInt->check([1, 2, 'c' => 3]));
        self::assertFalse($arrayStringInt->check(new ArrayIterator()));

        $arrayIteratorInteger = new CollectionType(
            new BuiltinType(BuiltinType::INT),
            null,
            new InstanceOfType(ArrayIterator::class)
        );
        self::assertTrue($arrayIteratorInteger->check(new ArrayIterator([1, 2, 'c' => 3])));
    }

    public function testStringify(): void
    {
        $integerCollection = new CollectionType(new BuiltinType(BuiltinType::INT));
        self::assertSame('int[]', (string)$integerCollection);

        $arrayStringInt = new CollectionType(
            new BuiltinType(BuiltinType::INT),
            new BuiltinType(BuiltinType::STRING),
            new BuiltinType(BuiltinType::ARRAY)
        );
        self::assertSame('array<string,int>', (string)$arrayStringInt);

        $arrayIteratorInteger = new CollectionType(
            new BuiltinType(BuiltinType::INT),
            null,
            new InstanceOfType(ArrayIterator::class)
        );
        self::assertSame('ArrayIterator<int>', (string)$arrayIteratorInteger);

        $arrayOfBoolOrInt = new CollectionType(
            new DisjunctionType([
                new BuiltinType(BuiltinType::BOOL),
                new BuiltinType(BuiltinType::INT),
            ]),
            null,
            new BuiltinType(BuiltinType::ARRAY)
        );
        self::assertSame('array<bool|int>', (string)$arrayOfBoolOrInt);
    }
}
