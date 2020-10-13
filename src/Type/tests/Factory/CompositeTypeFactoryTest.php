<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\CollectionType;
use spaceonfire\Type\ConjunctionType;
use spaceonfire\Type\DisjunctionType;
use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\InstanceOfType;
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
        self::assertFalse($factory->supports('[]'));
        self::assertFalse($factory->supports('unknown'));
    }

    public function testMake(): void
    {
        $factory = CompositeTypeFactory::makeWithDefaultFactories();

        self::assertInstanceOf(CollectionType::class, $factory->make('int[]'));
        self::assertInstanceOf(ConjunctionType::class, $factory->make(JsonSerializable::class . '&' . Traversable::class));
        self::assertInstanceOf(DisjunctionType::class, $factory->make(JsonSerializable::class . '|' . Traversable::class));
        self::assertInstanceOf(InstanceOfType::class, $factory->make(JsonSerializable::class));
        self::assertInstanceOf(BuiltinType::class, $factory->make('int'));
    }

    public function testMakeException(): void
    {
        $factory = CompositeTypeFactory::makeWithDefaultFactories();

        $this->expectException(TypeNotSupportedException::class);
        $factory->make('unknown');
    }
}
