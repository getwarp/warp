<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use stdClass;

class InstanceOfTypeTest extends TestCase
{
    public function testSupports(): void
    {
        self::assertTrue(InstanceOfType::supports(JsonSerializable::class));
        self::assertTrue(InstanceOfType::supports(stdClass::class));
        self::assertFalse(InstanceOfType::supports('NonExistingClass'));
    }

    public function testCreate(): void
    {
        InstanceOfType::create(JsonSerializable::class);
        self::assertTrue(true);
    }

    public function testCreateFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        InstanceOfType::create('NonExistingClass');
    }

    public function testCheck(): void
    {
        $type = InstanceOfType::create(stdClass::class);
        self::assertTrue($type->check((object)[]));
        self::assertFalse($type->check([]));
    }

    public function testStringify(): void
    {
        $type = InstanceOfType::create(stdClass::class);
        self::assertEquals(stdClass::class, (string)$type);
    }
}
