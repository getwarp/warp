<?php

declare(strict_types=1);

namespace Warp\Type\Factory;

use PHPUnit\Framework\TestCase;
use Warp\Type\Exception\TypeNotSupportedException;
use Warp\Type\VoidType;

class VoidTypeFactoryTest extends TestCase
{
    public function testSupports(): void
    {
        $factory = new VoidTypeFactory();
        self::assertTrue($factory->supports('void'));
        self::assertFalse($factory->supports('not-void'));
    }

    public function testMake(): void
    {
        $factory = new VoidTypeFactory();
        self::assertInstanceOf(VoidType::class, $factory->make('void'));
    }

    public function testMakeException(): void
    {
        $factory = new VoidTypeFactory();

        $this->expectException(TypeNotSupportedException::class);
        $factory->make('not-void');
    }
}
