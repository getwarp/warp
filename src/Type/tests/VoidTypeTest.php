<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use PHPUnit\Framework\TestCase;

class VoidTypeTest extends TestCase
{
    public function testCheck(): void
    {
        $type = new VoidType();

        $this->expectException(\RuntimeException::class);
        $type->check(null);
    }

    public function testToString(): void
    {
        $type = new VoidType();
        self::assertSame('void', (string)$type);
    }
}
