<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use Iterator;
use JsonSerializable;
use Traversable;

class ConjunctionTypeTest extends AbstractTestCase
{
    public function testFactoryOneArgument(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ConjunctionType::new(
            BuiltinType::new(BuiltinType::INT),
        );
    }

    public function testCheck(): void
    {
        $type = ConjunctionType::new(
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
        $type = ConjunctionType::new(
            InstanceOfType::new(JsonSerializable::class),
            InstanceOfType::new(Traversable::class),
        );

        self::assertSame(JsonSerializable::class . '&' . Traversable::class, (string)$type);
    }
}
