<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use Prophecy\Argument;
use spaceonfire\Type\AbstractTestCase;
use spaceonfire\Type\TypeInterface;

class MemoizedTypeFactoryTest extends AbstractTestCase
{
    public function testSupports(): void
    {
        $underlyingFactory = $this->prophesize(TypeFactoryInterface::class);
        $underlyingFactory->setParent(Argument::any());
        $underlyingFactory->supports('supported-type')->willReturn(true)->shouldBeCalledOnce();
        $underlyingFactory->supports('unsupported-type')->willReturn(false)->shouldBeCalledOnce();

        $factory = new MemoizedTypeFactory($underlyingFactory->reveal());

        self::assertTrue($factory->supports('supported-type'));
        self::assertTrue($factory->supports('supported-type'));
        self::assertFalse($factory->supports('unsupported-type'));
        self::assertFalse($factory->supports('unsupported-type'));
    }

    public function testMake(): void
    {
        $expectedReturn = $this->prophesize(TypeInterface::class)->reveal();
        $underlyingFactory = $this->prophesize(TypeFactoryInterface::class);
        $underlyingFactory->setParent(Argument::any());
        $underlyingFactory->make('supported-type')->willReturn($expectedReturn)->shouldBeCalledOnce();

        $factory = new MemoizedTypeFactory($underlyingFactory->reveal());

        self::assertSame($expectedReturn, $factory->make('supported-type'));
        self::assertSame($expectedReturn, $factory->make('supported-type'));
    }

    public function testSetParent(): void
    {
        $underlyingFactory = $this->prophesize(TypeFactoryInterface::class);
        $underlyingFactory->setParent(Argument::any())->shouldBeCalled();
        $factory = new MemoizedTypeFactory($underlyingFactory->reveal());
        $factory->setParent($factory);
    }
}
