<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use spaceonfire\Container\Exception\ContainerException;

class AbstractContainerDecoratorTest extends TestCase
{
    private function factory(ContainerInterface $container)
    {
        return new class($container) extends AbstractContainerDecorator {
        };
    }

    public function testGet(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->get('foo', Argument::type('array'))->willReturn('bar');

        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        self::assertSame('bar', $this->factory($container)->get('foo'));
    }

    public function testHas(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->has(Argument::any())->willReturn(false);
        $containerProphecy->has('foo')->willReturn(true);

        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $decorated = $this->factory($container);

        self::assertTrue($decorated->has('foo'));
        self::assertFalse($decorated->has('bar'));
    }

    public function testAdd(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->add(Argument::type('string'), Argument::any(), Argument::type('bool'))->shouldBeCalled();

        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $decorated = $this->factory($container);

        $decorated->add('foo', 'bar');
    }

    public function testShare(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->share(Argument::type('string'), Argument::any())->shouldBeCalled();

        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $decorated = $this->factory($container);

        $decorated->share('foo', 'bar');
    }

    public function testMake(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->make(Argument::type('string'), Argument::type('array'))->shouldBeCalled();

        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $decorated = $this->factory($container);

        $decorated->make('foo');
    }

    public function testInvoke(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->invoke(Argument::type('callable'), Argument::type('array'))->shouldBeCalled();

        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $decorated = $this->factory($container);

        $decorated->invoke(static function () {
        });
    }

    public function testAddServiceProvider(): void
    {
        $containerProphecy = $this->prophesize(ContainerWithServiceProvidersInterface::class);
        $containerProphecy->addServiceProvider(Argument::any())->shouldBeCalled();

        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $decorated = $this->factory($container);

        $decorated->addServiceProvider('foo');
    }

    public function testAddServiceProviderFailed(): void
    {
        $this->expectException(ContainerException::class);

        $containerProphecy = $this->prophesize(ContainerInterface::class);
        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $decorated = $this->factory($container);

        $decorated->addServiceProvider('foo');
    }
}
