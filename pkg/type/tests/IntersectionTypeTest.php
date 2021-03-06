<?php

declare(strict_types=1);

namespace Warp\Type;

use Iterator;
use JsonSerializable;
use Traversable;

class IntersectionTypeTest extends AbstractTestCase
{
    public function testFactoryOneArgument(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        IntersectionType::new(
            BuiltinType::new(BuiltinType::INT),
        );
    }

    public function testCheck(): void
    {
        $type = IntersectionType::new(
            InstanceOfType::new(JsonSerializable::class),
            InstanceOfType::new(Traversable::class),
        );

        $jsonSerializable = $this->prophesize(JsonSerializable::class)->reveal();
        $jsonSerializableAndTraversable = $this->prophesize(JsonSerializable::class)->willImplement(Iterator::class)->reveal();

        self::assertTrue($type->check($jsonSerializableAndTraversable));
        self::assertFalse($type->check($jsonSerializable));
    }

    public function testStringify(): void
    {
        $type = IntersectionType::new(
            InstanceOfType::new(JsonSerializable::class),
            InstanceOfType::new(Traversable::class),
        );

        self::assertSame(JsonSerializable::class . '&' . Traversable::class, (string)$type);
    }
}
