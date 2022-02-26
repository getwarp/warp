<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\CollectionType;
use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\InstanceOfType;
use spaceonfire\Type\IntersectionType;
use spaceonfire\Type\MixedType;
use spaceonfire\Type\UnionType;
use spaceonfire\Type\VoidType;
use Traversable;

class TypeFactoryAggregateTest extends TestCase
{
    public function testSupports(): void
    {
        $factory = TypeFactoryAggregate::default();

        self::assertTrue($factory->supports('int[]'));
        self::assertTrue($factory->supports(JsonSerializable::class . '&' . Traversable::class));
        self::assertTrue($factory->supports(JsonSerializable::class . '|' . Traversable::class));
        self::assertTrue($factory->supports(JsonSerializable::class));
        self::assertTrue($factory->supports('int'));
        self::assertTrue($factory->supports('mixed'));
        self::assertTrue($factory->supports('void'));
        self::assertFalse($factory->supports('[]'));
        self::assertFalse($factory->supports('unknown'));
        self::assertTrue($factory->supports('int|string|array<bool|int>|string|null'));
        self::assertTrue($factory->supports('int | string|array < bool | int >|string  |null'));
    }

    public function testMake(): void
    {
        $factory = TypeFactoryAggregate::default();

        self::assertInstanceOf(CollectionType::class, $factory->make('int[]'));
        self::assertInstanceOf(IntersectionType::class, $factory->make(JsonSerializable::class . '&' . Traversable::class));
        self::assertInstanceOf(UnionType::class, $factory->make(JsonSerializable::class . '|' . Traversable::class));
        self::assertInstanceOf(InstanceOfType::class, $factory->make(JsonSerializable::class));
        self::assertInstanceOf(BuiltinType::class, $factory->make('int'));
        self::assertInstanceOf(MixedType::class, $factory->make('mixed'));
        self::assertInstanceOf(VoidType::class, $factory->make('void'));
        self::assertInstanceOf(UnionType::class, $factory->make('int|string|array<bool|int>|string|null'));
    }

    public function testMakeException(): void
    {
        $factory = TypeFactoryAggregate::default();

        $this->expectException(TypeNotSupportedException::class);
        $factory->make('unknown');
    }
}
