<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use ArrayIterator;
use PHPUnit\Framework\TestCase;

class CollectionTypeTest extends TestCase
{
    public function testCheck(): void
    {
        $integerCollection = CollectionType::new(BuiltinType::new(BuiltinType::INT));
        self::assertTrue($integerCollection->check([1, 2, 'c' => 3]));
        self::assertFalse($integerCollection->check(['a', 'b', 'c']));

        $arrayStringInt = CollectionType::new(
            BuiltinType::new(BuiltinType::INT),
            BuiltinType::new(BuiltinType::STRING),
            BuiltinType::new(BuiltinType::ARRAY)
        );
        self::assertTrue($arrayStringInt->check(['a' => 1, 'b' => 2, 'c' => 3]));
        self::assertFalse($arrayStringInt->check([1, 2, 'c' => 3]));
        self::assertFalse($arrayStringInt->check(new ArrayIterator()));

        $arrayIteratorInteger = CollectionType::new(
            BuiltinType::new(BuiltinType::INT),
            null,
            InstanceOfType::new(ArrayIterator::class)
        );
        self::assertTrue($arrayIteratorInteger->check(new ArrayIterator([1, 2, 'c' => 3])));
    }

    public function testStringify(): void
    {
        $integerCollection = CollectionType::new(BuiltinType::new(BuiltinType::INT));
        self::assertSame('int[]', (string)$integerCollection);

        $arrayStringInt = CollectionType::new(
            BuiltinType::new(BuiltinType::INT),
            BuiltinType::new(BuiltinType::STRING),
            BuiltinType::new(BuiltinType::ARRAY)
        );
        self::assertSame('array<string,int>', (string)$arrayStringInt);

        $arrayIteratorInteger = CollectionType::new(
            BuiltinType::new(BuiltinType::INT),
            null,
            InstanceOfType::new(ArrayIterator::class)
        );
        self::assertSame('ArrayIterator<int>', (string)$arrayIteratorInteger);

        $arrayOfBoolOrInt = CollectionType::new(
            DisjunctionType::new(
                BuiltinType::new(BuiltinType::BOOL),
                BuiltinType::new(BuiltinType::INT),
            ),
            null,
            BuiltinType::new(BuiltinType::ARRAY)
        );
        self::assertSame('array<bool|int>', (string)$arrayOfBoolOrInt);
    }
}
