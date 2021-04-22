<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use PHPUnit\Framework\TestCase;

class MixedTypeTest extends TestCase
{
    public function testCheck(): void
    {
        $type = MixedType::new();
        self::assertTrue($type->check(true));
        self::assertTrue($type->check(42));
        self::assertTrue($type->check('string'));
        self::assertTrue($type->check(42.0));
        self::assertTrue($type->check([]));
        self::assertTrue($type->check((object)[]));
    }

    public function testToString(): void
    {
        $type = MixedType::new();
        self::assertSame('mixed', (string)$type);
    }
}
