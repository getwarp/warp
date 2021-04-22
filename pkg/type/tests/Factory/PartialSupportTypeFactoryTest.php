<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use PHPUnit\Framework\TestCase;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\Exception\TypeNotSupportedException;

class PartialSupportTypeFactoryTest extends TestCase
{
    public function testSupports(): void
    {
        $decoratedFactory = new BuiltinTypeFactory();
        $factory = new PartialSupportTypeFactory($decoratedFactory, fn ($type) => $type === BuiltinType::INT);

        self::assertTrue($decoratedFactory->supports(BuiltinType::INT));
        self::assertTrue($factory->supports(BuiltinType::INT));
        self::assertTrue($decoratedFactory->supports(BuiltinType::STRING));
        self::assertFalse($factory->supports(BuiltinType::STRING));
    }

    public function testMake(): void
    {
        $decoratedFactory = new BuiltinTypeFactory();
        $factory = new PartialSupportTypeFactory($decoratedFactory, fn ($type) => $type === BuiltinType::INT);

        self::assertEquals(
            $decoratedFactory->make(BuiltinType::INT),
            $factory->make(BuiltinType::INT)
        );
    }

    public function testMakeException(): void
    {
        $decoratedFactory = new BuiltinTypeFactory();
        $factory = new PartialSupportTypeFactory($decoratedFactory, fn ($type) => $type === BuiltinType::INT);

        $decoratedFactory->make(BuiltinType::STRING);

        $this->expectException(TypeNotSupportedException::class);
        $factory->make(BuiltinType::STRING);
    }
}
