<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class InstanceOfTypeTest extends TestCase
{
    public function testConstructFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InstanceOfType('unknown class');
    }

    public function testCheck(): void
    {
        $type = new InstanceOfType(stdClass::class);
        self::assertTrue($type->check((object)[]));
        self::assertFalse($type->check([]));
    }

    public function testStringify(): void
    {
        $type = new InstanceOfType(stdClass::class);
        self::assertSame(stdClass::class, (string)$type);
    }
}
