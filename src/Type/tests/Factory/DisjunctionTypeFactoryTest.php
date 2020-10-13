<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use spaceonfire\Type\DisjunctionType;
use spaceonfire\Type\Exception\TypeNotSupportedException;
use Traversable;

class DisjunctionTypeFactoryTest extends TestCase
{
    private function makeFactory(): DisjunctionTypeFactory
    {
        $factory = new DisjunctionTypeFactory();
        $factory->setParent(CompositeTypeFactory::makeWithDefaultFactories());
        return $factory;
    }

    public function testSupports(): void
    {
        $factory = $this->makeFactory();
        self::assertTrue($factory->supports(JsonSerializable::class . '|' . Traversable::class));
        self::assertFalse($factory->supports(JsonSerializable::class . '|unknown'));
        self::assertFalse($factory->supports(JsonSerializable::class));
    }

    public function testNoSupportsWithoutParent(): void
    {
        $factory = new DisjunctionTypeFactory();
        self::assertFalse($factory->supports(JsonSerializable::class . '|' . Traversable::class));
    }

    public function testMake(): void
    {
        $factory = $this->makeFactory();
        self::assertInstanceOf(DisjunctionType::class, $factory->make(JsonSerializable::class . '|' . Traversable::class));
    }

    public function testMakeException(): void
    {
        $factory = $this->makeFactory();

        $this->expectException(TypeNotSupportedException::class);
        $factory->make(JsonSerializable::class);
    }
}
