<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use PHPUnit\Framework\TestCase;
use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\InstanceOfType;

class InstanceOfTypeFactoryTest extends TestCase
{
    public function testSupports(): void
    {
        $factory = new InstanceOfTypeFactory();
        self::assertTrue($factory->supports(\stdClass::class));
        self::assertTrue($factory->supports(\Traversable::class));
        self::assertFalse($factory->supports('not a class'));
    }

    public function testMake(): void
    {
        $factory = new InstanceOfTypeFactory();
        self::assertInstanceOf(InstanceOfType::class, $factory->make(\stdClass::class));
    }

    public function testMakeException(): void
    {
        $factory = new InstanceOfTypeFactory();

        $this->expectException(TypeNotSupportedException::class);
        $factory->make('not a class');
    }
}
