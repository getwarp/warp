<?php

declare(strict_types=1);

namespace Warp\Type\Factory;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Warp\Type\Exception\TypeNotSupportedException;
use Warp\Type\UnionType;
use Traversable;

class UnionTypeFactoryTest extends TestCase
{
    private function makeFactory(): UnionTypeFactory
    {
        $factory = new UnionTypeFactory();
        $factory->setParent(TypeFactoryAggregate::default());
        return $factory;
    }

    public function testSupports(): void
    {
        $factory = $this->makeFactory();
        self::assertTrue($factory->supports(JsonSerializable::class . '|' . Traversable::class));
        self::assertFalse($factory->supports(JsonSerializable::class . '|unknown'));
        self::assertFalse($factory->supports(JsonSerializable::class));
        self::assertFalse($factory->supports('array<unexpected_end|unknown'));
    }

    public function testNoSupportsWithoutParent(): void
    {
        $factory = new UnionTypeFactory();
        self::assertFalse($factory->supports(JsonSerializable::class . '|' . Traversable::class));
    }

    public function testMake(): void
    {
        $factory = $this->makeFactory();
        self::assertInstanceOf(UnionType::class, $factory->make(JsonSerializable::class . '|' . Traversable::class));
    }

    public function testMakeException(): void
    {
        $factory = $this->makeFactory();

        $this->expectException(TypeNotSupportedException::class);
        $factory->make(JsonSerializable::class);
    }
}
