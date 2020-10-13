<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use PHPUnit\Framework\TestCase;

class DisjunctionTypeTest extends TestCase
{
    public function testCheck(): void
    {
        $type = new DisjunctionType([
            new BuiltinType(BuiltinType::INT),
            new BuiltinType(BuiltinType::NULL),
        ]);
        self::assertTrue($type->check(1));
        self::assertTrue($type->check(null));
        self::assertFalse($type->check('1'));
    }

    public function testStringify(): void
    {
        $type = new DisjunctionType([
            new BuiltinType(BuiltinType::INT),
            new BuiltinType(BuiltinType::NULL),
        ]);
        self::assertEquals('int|null', (string)$type);
    }
}
