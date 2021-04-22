<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use PHPUnit\Framework\TestCase;

class VoidTypeTest extends TestCase
{
    public function testCheck(): void
    {
        $type = VoidType::new();

        $this->expectException(\LogicException::class);
        $type->check(null);
    }

    public function testToString(): void
    {
        $type = VoidType::new();
        self::assertSame('void', (string)$type);
    }
}
