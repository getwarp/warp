<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use PHPUnit\Framework\TestCase;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\Exception\TypeNotSupportedException;
use stdClass;

class BuiltinTypeFactoryTest extends TestCase
{
    public function testSupports(): void
    {
        $factory = new BuiltinTypeFactory();
        self::assertTrue($factory->supports('int'));
        self::assertTrue($factory->supports('integer'));
        self::assertTrue($factory->supports('bool'));
        self::assertTrue($factory->supports('boolean'));
        self::assertTrue($factory->supports('float'));
        self::assertTrue($factory->supports('double'));
        self::assertTrue($factory->supports('string'));
        self::assertTrue($factory->supports('resource'));
        self::assertTrue($factory->supports('resource (closed)'));
        self::assertTrue($factory->supports('null'));
        self::assertTrue($factory->supports('object'));
        self::assertTrue($factory->supports('array'));
        self::assertTrue($factory->supports('callable'));
        self::assertTrue($factory->supports('iterable'));
        self::assertFalse($factory->supports('unknown'));
        self::assertFalse($factory->supports(stdClass::class));
    }

    public function testMake(): void
    {
        $factory = new BuiltinTypeFactory();
        $integerType = $factory->make('integer');
        self::assertInstanceOf(BuiltinType::class, $integerType);
        self::assertTrue($integerType->check(42));
        self::assertFalse($integerType->check('42'));

        $objectType = $factory->make('object');
        self::assertInstanceOf(BuiltinType::class, $objectType);
        self::assertTrue($objectType->check((object)[]));
    }

    public function testMakeException(): void
    {
        $factory = new BuiltinTypeFactory();

        $this->expectException(TypeNotSupportedException::class);
        $factory->make('unknown');
    }
}
