<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use PHPUnit\Framework\TestCase;
use spaceonfire\Type\CollectionType;
use spaceonfire\Type\Exception\TypeNotSupportedException;

class CollectionTypeFactoryTest extends TestCase
{
    private function makeFactory(?TypeFactoryInterface $iterableTypeFactory = null): CollectionTypeFactory
    {
        $factory = new CollectionTypeFactory($iterableTypeFactory);
        $factory->setParent(TypeFactoryAggregate::default());
        return $factory;
    }

    private function makeIterableTypeFactory(): TypeFactoryInterface
    {
        return new PartialSupportTypeFactory(new BuiltinTypeFactory(), fn (string $type): bool => in_array($type, ['array', 'iterable'], true));
    }

    public function testSupports(): void
    {
        $factory = $this->makeFactory();

        self::assertTrue($factory->supports('int[]'));
        self::assertTrue($factory->supports('array<int>'));
        self::assertTrue($factory->supports('iterable<string,int>'));
        self::assertTrue($factory->supports('(string|int)[]'));
        self::assertTrue($factory->supports('ArrayIterator<int>'));
        self::assertTrue($factory->supports('Traversable<int>'));
        self::assertFalse($factory->supports('[]'));
        self::assertFalse($factory->supports('<>'));
        self::assertFalse($factory->supports('ArrayIterator<>'));
        self::assertFalse($factory->supports('stdClass<>'));
        self::assertFalse($factory->supports('string<string>'));
        self::assertFalse($factory->supports('array<unknow>'));
        self::assertFalse($factory->supports('array<unknown,int>'));
        self::assertFalse($factory->supports('array<unexpected_end'));
    }

    public function testSupportsWithCustomIterableTypeFactory(): void
    {
        $factory = $this->makeFactory($this->makeIterableTypeFactory());
        self::assertTrue($factory->supports('int[]'));
        self::assertTrue($factory->supports('array<int>'));
        self::assertFalse($factory->supports('Traversable<int>'));
    }

    public function testNoSupportsWithoutParent(): void
    {
        $factory = new CollectionTypeFactory();
        self::assertFalse($factory->supports('int[]'));
        self::assertFalse($factory->supports('array<int>'));
    }

    public function testMake(): void
    {
        $factory = $this->makeFactory();
        self::assertInstanceOf(CollectionType::class, $factory->make('int[]'));
    }

    public function testMakeException(): void
    {
        $factory = $this->makeFactory();

        $this->expectException(TypeNotSupportedException::class);
        $factory->make('<>');
    }
}
