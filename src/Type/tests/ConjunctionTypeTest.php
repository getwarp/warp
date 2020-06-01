<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;
use Iterator;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Traversable;

class ConjunctionTypeTest extends TestCase
{
    public function testSupports(): void
    {
        self::assertTrue(ConjunctionType::supports(JsonSerializable::class . '&' . Traversable::class));
        self::assertFalse(ConjunctionType::supports(JsonSerializable::class));
    }

    public function testCreate(): void
    {
        ConjunctionType::create(JsonSerializable::class . '&' . Traversable::class);
        self::assertTrue(true);
    }

    public function testCreateFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ConjunctionType::create(JsonSerializable::class);
    }

    public function testCheck(): void
    {
        $type = ConjunctionType::create(JsonSerializable::class . '&' . Traversable::class);

        $jsonSerializable = $this->prophesize(JsonSerializable::class)->reveal();
        $jsonSerializableAndTraversable = $this->prophesize(JsonSerializable::class)->willImplement(Iterator::class)->reveal();

        self::assertTrue($type->check($jsonSerializableAndTraversable));
        self::assertFalse($type->check($jsonSerializable));
    }

    public function testStringify(): void
    {
        $type = ConjunctionType::create(JsonSerializable::class . '&' . Traversable::class);
        self::assertEquals(JsonSerializable::class . '&' . Traversable::class, (string)$type);
    }
}
