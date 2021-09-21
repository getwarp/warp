<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use stdClass;
use Traversable;

class BackwardCompatibilityTest extends TestCase
{
    protected function setUp(): void
    {
//        self::markTestSkipped();
    }

    public function testBuiltinTypeSupports(): void
    {
        self::assertTrue(BuiltinType::supports('int'));
        self::assertTrue(BuiltinType::supports('integer'));
        self::assertTrue(BuiltinType::supports('bool'));
        self::assertTrue(BuiltinType::supports('boolean'));
        self::assertTrue(BuiltinType::supports('float'));
        self::assertTrue(BuiltinType::supports('double'));
        self::assertTrue(BuiltinType::supports('string'));
        self::assertTrue(BuiltinType::supports('resource'));
        self::assertTrue(BuiltinType::supports('resource (closed)'));
        self::assertTrue(BuiltinType::supports('null'));
        self::assertTrue(BuiltinType::supports('object'));
        self::assertTrue(BuiltinType::supports('array'));
        self::assertTrue(BuiltinType::supports('callable'));
        self::assertTrue(BuiltinType::supports('iterable'));
        self::assertFalse(BuiltinType::supports('unknown'));
        self::assertFalse(BuiltinType::supports(stdClass::class));
    }

    public function testBuiltinTypeCreate(): void
    {
        BuiltinType::create('integer');
        self::assertTrue(true);
    }

    public function testBuiltinTypeCreateFail(): void
    {
        $this->expectException(Exception\TypeNotSupportedException::class);
        BuiltinType::create('unknown type');
    }

    public function testCollectionTypeSupports(): void
    {
        self::assertTrue(CollectionType::supports('int[]'));
        self::assertTrue(CollectionType::supports('array<int>'));
        self::assertTrue(CollectionType::supports('iterable<string,int>'));
        self::assertTrue(CollectionType::supports('ArrayIterator<int>'));
        self::assertTrue(CollectionType::supports('Traversable<int>'));
        self::assertFalse(CollectionType::supports('[]'));
        self::assertFalse(CollectionType::supports('<>'));
        self::assertFalse(CollectionType::supports('ArrayIterator<>'));
        self::assertFalse(CollectionType::supports('stdClass<>'));
        self::assertFalse(CollectionType::supports('string<string>'));
    }

    public function testCollectionTypeCreate(): void
    {
        CollectionType::create('int[]');
        CollectionType::create('array<int>');
        CollectionType::create('iterable<string,int>');
        CollectionType::create('ArrayIterator<int>');
        CollectionType::create('Traversable<int>');
        self::assertTrue(true);
    }

    public function testCollectionTypeCreateNotSupported(): void
    {
        $this->expectException(Exception\TypeNotSupportedException::class);
        CollectionType::create('<>');
    }

    public function testConjunctionTypeSupports(): void
    {
        self::assertTrue(ConjunctionType::supports(JsonSerializable::class . '&' . Traversable::class));
        self::assertFalse(ConjunctionType::supports(JsonSerializable::class));
    }

    public function testConjunctionTypeCreate(): void
    {
        ConjunctionType::create(JsonSerializable::class . '&' . Traversable::class);
        self::assertTrue(true);
    }

    public function testConjunctionTypeCreateFail(): void
    {
        $this->expectException(Exception\TypeNotSupportedException::class);
        ConjunctionType::create(JsonSerializable::class);
    }

    public function testDisjunctionTypeSupports(): void
    {
        self::assertTrue(DisjunctionType::supports('int|null'));
        self::assertFalse(DisjunctionType::supports('int'));
    }

    public function testDisjunctionTypeCreate(): void
    {
        DisjunctionType::create('int|null');
        self::assertTrue(true);
    }

    public function testDisjunctionTypeCreateFail(): void
    {
        $this->expectException(Exception\TypeNotSupportedException::class);
        DisjunctionType::create('int');
    }

    public function testInstanceOfTypeSupports(): void
    {
        self::assertTrue(InstanceOfType::supports(JsonSerializable::class));
        self::assertTrue(InstanceOfType::supports(stdClass::class));
        self::assertFalse(InstanceOfType::supports('NonExistingClass'));
    }

    public function testInstanceOfTypeCreate(): void
    {
        InstanceOfType::create(JsonSerializable::class);
        self::assertTrue(true);
    }

    public function testInstanceOfTypeCreateFail(): void
    {
        $this->expectException(Exception\TypeNotSupportedException::class);
        InstanceOfType::create('NonExistingClass');
    }

    public function testTypeFactoryCreate()
    {
        self::assertInstanceOf(BuiltinType::class, TypeFactory::create('integer'));
        self::assertInstanceOf(InstanceOfType::class, TypeFactory::create(stdClass::class));
        self::assertInstanceOf(DisjunctionType::class, TypeFactory::create(stdClass::class . '|null'));
        self::assertInstanceOf(ConjunctionType::class, TypeFactory::create(stdClass::class . '&object'));
    }

    public function testTypeFactoryCreateFail()
    {
        $this->expectException(Exception\TypeNotSupportedException::class);
        TypeFactory::create('unknown type');
    }
}
