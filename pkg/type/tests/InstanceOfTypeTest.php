<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use PHPUnit\Framework\TestCase;

class InstanceOfTypeTest extends TestCase
{
    public function testCheck(): void
    {
        $type = InstanceOfType::new(\stdClass::class);
        self::assertTrue($type->check((object)[]));
        self::assertFalse($type->check([]));
    }

    public function testStringify(): void
    {
        $type = InstanceOfType::new(\stdClass::class);
        self::assertSame(\stdClass::class, (string)$type);
    }
}
