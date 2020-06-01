<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class TypeFactoryTest extends TestCase
{
    public function testCreate()
    {
        self::assertInstanceOf(BuiltinType::class, TypeFactory::create('integer'));
        self::assertInstanceOf(InstanceOfType::class, TypeFactory::create(stdClass::class));
        self::assertInstanceOf(DisjunctionType::class, TypeFactory::create(stdClass::class . '|null'));
        self::assertInstanceOf(ConjunctionType::class, TypeFactory::create(stdClass::class . '&object'));
    }

    public function testCreateFail()
    {
        $this->expectException(InvalidArgumentException::class);
        self::assertInstanceOf(BuiltinType::class, TypeFactory::create('unknown type'));
    }
}
