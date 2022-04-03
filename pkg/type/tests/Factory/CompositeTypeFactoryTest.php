<?php

declare(strict_types=1);

namespace Warp\Type\Factory;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Warp\Type\BuiltinType;
use Warp\Type\CollectionType;
use Warp\Type\ConjunctionType;
use Warp\Type\DisjunctionType;
use Warp\Type\Exception\TypeNotSupportedException;
use Warp\Type\InstanceOfType;
use Warp\Type\MixedType;
use Warp\Type\VoidType;
use Traversable;

class CompositeTypeFactoryTest extends TestCase
{
    public function testSupports(): void
    {
        $factory = CompositeTypeFactory::makeWithDefaultFactories();

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
        $factory = CompositeTypeFactory::makeWithDefaultFactories();

        self::assertInstanceOf(CollectionType::class, $factory->make('int[]'));
        self::assertInstanceOf(ConjunctionType::class, $factory->make(JsonSerializable::class . '&' . Traversable::class));
        self::assertInstanceOf(DisjunctionType::class, $factory->make(JsonSerializable::class . '|' . Traversable::class));
        self::assertInstanceOf(InstanceOfType::class, $factory->make(JsonSerializable::class));
        self::assertInstanceOf(BuiltinType::class, $factory->make('int'));
        self::assertInstanceOf(MixedType::class, $factory->make('mixed'));
        self::assertInstanceOf(VoidType::class, $factory->make('void'));
        self::assertInstanceOf(DisjunctionType::class, $factory->make('int|string|array<bool|int>|string|null'));
    }

    public function testMakeException(): void
    {
        $factory = CompositeTypeFactory::makeWithDefaultFactories();

        $this->expectException(TypeNotSupportedException::class);
        $factory->make('unknown');
    }
}
