<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use spaceonfire\Type\ConjunctionType;
use spaceonfire\Type\Exception\TypeNotSupportedException;
use Traversable;

class ConjunctionTypeFactoryTest extends TestCase
{
    private function makeFactory(): ConjunctionTypeFactory
    {
        $factory = new ConjunctionTypeFactory();
        $factory->setParent(TypeFactoryAggregate::default());
        return $factory;
    }

    public function testSupports(): void
    {
        $factory = $this->makeFactory();
        self::assertTrue($factory->supports(JsonSerializable::class . '&' . Traversable::class));
        self::assertFalse($factory->supports(JsonSerializable::class . '&unknown'));
        self::assertFalse($factory->supports(JsonSerializable::class));
        self::assertFalse($factory->supports('array<unexpected_end|unknown'));
    }

    public function testNoSupportsWithoutParent(): void
    {
        $factory = new ConjunctionTypeFactory();
        self::assertFalse($factory->supports(JsonSerializable::class . '&' . Traversable::class));
    }

    public function testMake(): void
    {
        $factory = $this->makeFactory();
        self::assertInstanceOf(ConjunctionType::class, $factory->make(JsonSerializable::class . '&' . Traversable::class));
    }

    public function testMakeException(): void
    {
        $factory = $this->makeFactory();

        $this->expectException(TypeNotSupportedException::class);
        $factory->make(JsonSerializable::class);
    }
}
