<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use PHPUnit\Framework\TestCase;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\ConjunctionType;
use spaceonfire\Type\DisjunctionType;
use spaceonfire\Type\Exception\TypeNotSupportedException;

class GroupTypeFactoryTest extends TestCase
{
    private function makeFactory(): GroupTypeFactory
    {
        $factory = new GroupTypeFactory();
        $factory->setParent(TypeFactoryAggregate::default());
        return $factory;
    }

    public function testSupports(): void
    {
        $factory = $this->makeFactory();

        self::assertTrue($factory->supports('(int)'));
        self::assertTrue($factory->supports('(int|string)'));
        self::assertTrue($factory->supports('(int&string)'));
        self::assertFalse($factory->supports('(int&string'));
        self::assertFalse($factory->supports('int&string)'));
        self::assertFalse($factory->supports('(unknown)'));
        self::assertFalse($factory->supports('()'));
    }

    public function testNoSupportsWithoutParent(): void
    {
        $factory = new GroupTypeFactory();
        self::assertFalse($factory->supports('(int)'));
    }

    public function testMake(): void
    {
        $factory = $this->makeFactory();

        self::assertInstanceOf(BuiltinType::class, $factory->make('(int)'));
        self::assertInstanceOf(DisjunctionType::class, $factory->make('(int|string)'));
        self::assertInstanceOf(ConjunctionType::class, $factory->make('(int&string)'));
    }

    public function testMakeException(): void
    {
        $factory = $this->makeFactory();

        $this->expectException(TypeNotSupportedException::class);
        $factory->make('(unknown)');
    }
}
