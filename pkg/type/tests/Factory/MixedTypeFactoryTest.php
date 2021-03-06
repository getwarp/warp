<?php

declare(strict_types=1);

namespace Warp\Type\Factory;

use PHPUnit\Framework\TestCase;
use Warp\Type\Exception\TypeNotSupportedException;
use Warp\Type\MixedType;

class MixedTypeFactoryTest extends TestCase
{
    public function testSupports(): void
    {
        $factory = new MixedTypeFactory();
        self::assertTrue($factory->supports('mixed'));
        self::assertFalse($factory->supports('not-mixed'));
    }

    public function testMake(): void
    {
        $factory = new MixedTypeFactory();
        self::assertInstanceOf(MixedType::class, $factory->make('mixed'));
    }

    public function testMakeException(): void
    {
        $factory = new MixedTypeFactory();

        $this->expectException(TypeNotSupportedException::class);
        $factory->make('not-mixed');
    }
}
