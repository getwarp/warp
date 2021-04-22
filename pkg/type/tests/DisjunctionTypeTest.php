<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use PHPUnit\Framework\TestCase;

class DisjunctionTypeTest extends TestCase
{
    public function testFactoryOneArgument(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DisjunctionType::new(
            BuiltinType::new(BuiltinType::INT),
        );
    }

    public function testCheck(): void
    {
        $type = DisjunctionType::new(
            BuiltinType::new(BuiltinType::INT),
            BuiltinType::new(BuiltinType::NULL),
        );
        self::assertTrue($type->check(1));
        self::assertTrue($type->check(null));
        self::assertFalse($type->check('1'));
    }

    public function testStringify(): void
    {
        $type = DisjunctionType::new(
            BuiltinType::new(BuiltinType::INT),
            BuiltinType::new(BuiltinType::NULL),
        );
        self::assertSame('int|null', (string)$type);
    }
}
