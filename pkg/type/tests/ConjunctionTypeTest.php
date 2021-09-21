<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use Iterator;
use JsonSerializable;
use Traversable;

class ConjunctionTypeTest extends AbstractTestCase
{
    public function testCheck(): void
    {
        $type = new ConjunctionType([
            new InstanceOfType(JsonSerializable::class),
            new InstanceOfType(Traversable::class),
        ]);

        $jsonSerializable = $this->prophesize(JsonSerializable::class)->reveal();
        $jsonSerializableAndTraversable = $this->prophesize(JsonSerializable::class)->willImplement(Iterator::class)->reveal();

        self::assertTrue($type->check($jsonSerializableAndTraversable));
        self::assertFalse($type->check($jsonSerializable));
    }

    public function testStringify(): void
    {
        $type = new ConjunctionType([
            new InstanceOfType(JsonSerializable::class),
            new InstanceOfType(Traversable::class),
        ]);

        self::assertSame(JsonSerializable::class . '&' . Traversable::class, (string)$type);
    }
}
