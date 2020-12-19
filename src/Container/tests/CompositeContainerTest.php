<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use spaceonfire\Collection\Collection;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\Exception\NotFoundException;
use spaceonfire\Container\Fixtures\AbstractClass\AbstractClass;
use spaceonfire\Container\Fixtures\AbstractClass\AcceptNullableAbstractClass;
use spaceonfire\Container\Fixtures\AbstractClass\RequiresAbstractClass;

class CompositeContainerTest extends TestCase
{
    use WithContainerMockTrait;

    public function testHas(): void
    {
        $composite = new CompositeContainer([
            $this->createContainerMock(['foo' => 'foo'], PsrContainerInterface::class)->reveal(),
            $this->createContainerMock(['bar' => 'bar'])->reveal(),
        ]);

        self::assertTrue($composite->has('foo'));
        self::assertTrue($composite->has('bar'));
        self::assertFalse($composite->has('baz'));
    }

    public function testGet(): void
    {
        $composite = new CompositeContainer([
            $this->createContainerMock(['foo' => 'foo'], PsrContainerInterface::class)->reveal(),
            $this->createContainerMock(['bar' => 'bar'])->reveal(),
        ]);

        self::assertSame('foo', $composite->get('foo'));
        self::assertSame('bar', $composite->get('bar'));

        $this->expectException(NotFoundException::class);
        $composite->get('baz');
    }

    public function testSetContainer(): void
    {
        $containerAware = $this->createContainerMock(['bar' => 'bar'], [ContainerInterface::class, ContainerAwareInterface::class]);
        $containerAware->setContainer(Argument::any())->shouldBeCalled();

        $composite = new CompositeContainer([
            $this->createContainerMock(['foo' => 'foo'], PsrContainerInterface::class)->reveal(),
            $containerAware->reveal(),
        ]);

        $composite->setContainer($composite);
    }

    public function testAddServiceProvider(): void
    {
        $composite = new CompositeContainer([]);

        $primary = $this->createContainerMock(['bar' => 'bar'], ContainerWithServiceProvidersInterface::class);
        $primary->addServiceProvider(Argument::any())->shouldBeCalled();

        $containers = [
            $this->createContainerMock([], PsrContainerInterface::class)->reveal(),
            $this->createContainerMock(['bar' => 'bar'])->reveal(),
            $primary->reveal(),
        ];

        $composite->addContainers($containers);

        $composite->addServiceProvider('provider');
    }

    public function testAddServiceProviderFailed(): void
    {
        $this->expectException(ContainerException::class);
        $composite = new CompositeContainer([
            $this->createContainerMock([], PsrContainerInterface::class)->reveal(),
            $this->createContainerMock(['bar' => 'bar'])->reveal(),
        ]);
        $composite->addServiceProvider('provider');
    }

    public function testNoPrimaryContainerInChain(): void
    {
        $this->expectException(ContainerException::class);
        $composite = new CompositeContainer([
            $this->createContainerMock([], PsrContainerInterface::class)->reveal(),
        ]);
        $composite->addServiceProvider('provider');
    }

    public function testMake(): void
    {
        $containerProphecy = $this->createContainerMock();
        $containerProphecy->make(Argument::type('string'), Argument::type('array'))->shouldBeCalled();

        $composite = new CompositeContainer([$containerProphecy->reveal()]);

        $composite->make('foo');
    }

    public function testInvoke(): void
    {
        $containerProphecy = $this->createContainerMock();
        $containerProphecy->invoke(Argument::type('callable'), Argument::type('array'))->shouldBeCalled();

        $composite = new CompositeContainer([$containerProphecy->reveal()]);

        $composite->invoke(static function () {
        });
    }

    public function testAdd(): void
    {
        $containerProphecy = $this->createContainerMock();
        $containerProphecy->add(Argument::type('string'), Argument::any(), Argument::type('bool'))->shouldBeCalled();

        $composite = new CompositeContainer([$containerProphecy->reveal()]);

        $composite->add('foo', 'bar');
    }

    public function testShare(): void
    {
        $containerProphecy = $this->createContainerMock();
        $containerProphecy->share(Argument::type('string'), Argument::any())->shouldBeCalled();

        $composite = new CompositeContainer([$containerProphecy->reveal()]);

        $composite->share('foo', 'bar');
    }

    public function testHasTagged(): void
    {
        $composite = new CompositeContainer([]);

        self::assertFalse($composite->hasTagged('tag'));

        $containerProphecy = $this->createContainerMock();
        $containerProphecy->hasTagged('tag')->willReturn(true)->shouldBeCalled();
        $composite->addContainer($containerProphecy->reveal());

        self::assertTrue($composite->hasTagged('tag'));
    }

    public function testGetTagged(): void
    {
        $composite = new CompositeContainer([]);

        self::assertTrue($composite->getTagged('tag')->isEmpty());

        $containerWithFoo = $this->createContainerMock();
        $containerWithFoo->hasTagged('tag')->willReturn(true);
        $containerWithFoo->getTagged('tag')->willReturn(new Collection(['foo']))->shouldBeCalled();
        $composite->addContainer($containerWithFoo->reveal());

        $containerWithBar = $this->createContainerMock();
        $containerWithBar->hasTagged('tag')->willReturn(true);
        $containerWithBar->getTagged('tag')->willReturn(new Collection(['bar']))->shouldBeCalled();
        $composite->addContainer($containerWithBar->reveal());

        // psr container without tags support should be skipped
        $composite->addContainer($this->createContainerMock([], PsrContainerInterface::class)->reveal());

        $resolved = $composite->getTagged('tag');

        self::assertFalse($resolved->isEmpty());

        $foo = $resolved->find(function ($v) {
            return $v === 'foo';
        });
        self::assertSame('foo', $foo);

        $bar = $resolved->find(function ($v) {
            return $v === 'bar';
        });
        self::assertSame('bar', $bar);
    }

    public function testAddContainersFromIterator(): void
    {
        $containers = [
            10 => $this->createContainerMock(['foo' => 'foo'], PsrContainerInterface::class)->reveal(),
            20 => $this->createContainerMock(['bar' => 'bar'])->reveal(),
            'baz' => $this->createContainerMock(['baz' => 'baz'])->reveal(),
        ];

        $composite = new CompositeContainer(new ArrayIterator($containers));

        self::assertCount(count($containers), $composite);
        foreach ($composite as $container) {
            self::assertContains($container, $containers);
        }
    }

    public function testContainersPriority(): void
    {
        $composite = new CompositeContainer();

        $fooContainer = $this->createContainerMock(['foo' => 'foo'])->reveal();
        $composite->addContainer($fooContainer, 10);

        self::assertSame('foo', $composite->get('foo'));

        $barContainer = $this->createContainerMock(['foo' => 'bar'])->reveal();
        $composite->addContainer($barContainer, 9);

        self::assertSame('bar', $composite->get('foo'));

        $bazContainer = $this->createContainerMock(['foo' => 'baz'])->reveal();
        $composite->addContainer($bazContainer, 8);

        self::assertSame('baz', $composite->get('foo'));
    }

    public function testGetAcceptNullableAbstractClass(): void
    {
        $composite = new CompositeContainer([
            new Container(),
            new ReflectionContainer(),
        ]);

        $object = $composite->get(AcceptNullableAbstractClass::class);

        self::assertNull($object->getAbstractClass());
    }

    public function testGetRequiresAbstractClass(): void
    {
        $composite = new CompositeContainer();

        $abstractClass = new class extends AbstractClass {
        };

        $abstractClassContainer = $this->createContainerMock([
            AbstractClass::class => $abstractClass,
        ])->reveal();

        $composite->addContainer($abstractClassContainer);
        $composite->addContainer(new ReflectionContainer());

        $object = $composite->get(RequiresAbstractClass::class);

        self::assertSame($abstractClass, $object->getAbstractClass());
    }
}
