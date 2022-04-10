<?php

declare(strict_types=1);

namespace Warp\Container;

use PHPUnit\Framework\TestCase;
use Warp\Container\Exception\ContainerException;
use Warp\Container\Exception\NotFoundException;
use Warp\Container\Factory\FactoryOptions;
use Warp\Container\Factory\Reflection\ReflectionFactoryAggregate;
use Warp\Container\Factory\Reflection\ReflectionInvoker;
use Warp\Container\Fixtures\ArrayContainer;
use Warp\Container\Fixtures\B;
use Warp\Container\Fixtures\MyClass;
use Warp\Container\Fixtures\ServiceProvider\MyClassProvider;

class CompositeContainerTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $factory = new ReflectionFactoryAggregate();
        $invoker = new ReflectionInvoker();
        $definitionContainer = new DefinitionContainer($factory, $invoker);
        $factoryContainer = new FactoryContainer($factory, $invoker);
        $compositeContainer = new CompositeContainer($definitionContainer, $factoryContainer);

        self::assertSame($compositeContainer, $definitionContainer->getContainer());
        self::assertSame($compositeContainer, $factoryContainer->getContainer());
    }

    public function testInterfacesImplementation(): void
    {
        $compositeContainer = new CompositeContainer();

        $factoryContainer = new FactoryContainer();

        $compositeContainer->addContainer($factoryContainer, 50);

        self::assertTrue($compositeContainer->has(FactoryAggregateInterface::class));
        self::assertTrue($compositeContainer->has(InvokerInterface::class));
        self::assertFalse($compositeContainer->has(ServiceProviderAggregateInterface::class));
        self::assertFalse($compositeContainer->has(DefinitionAggregateInterface::class));

        self::assertSame($compositeContainer, $compositeContainer->get(FactoryAggregateInterface::class));
        self::assertSame($compositeContainer, $compositeContainer->get(InvokerInterface::class));

        $definitionContainer = new DefinitionContainer();

        $compositeContainer->addContainer($definitionContainer, 10);

        self::assertTrue($compositeContainer->has(FactoryAggregateInterface::class));
        self::assertTrue($compositeContainer->has(InvokerInterface::class));
        self::assertTrue($compositeContainer->has(ServiceProviderAggregateInterface::class));
        self::assertTrue($compositeContainer->has(DefinitionAggregateInterface::class));

        self::assertSame($compositeContainer, $compositeContainer->get(FactoryAggregateInterface::class));
        self::assertSame($compositeContainer, $compositeContainer->get(InvokerInterface::class));
    }

    public function testNoInterfacesImplementation(): void
    {
        $compositeContainer = new CompositeContainer(new ArrayContainer([]));

        $this->expectException(ContainerException::class);

        $compositeContainer->make(MyClass::class);
    }

    public function testHas(): void
    {
        $composite = new CompositeContainer(
            new ArrayContainer(['foo' => 'foo']),
            new ArrayContainer(['bar' => 'bar']),
        );

        self::assertTrue($composite->has('foo'));
        self::assertTrue($composite->has('bar'));
        self::assertFalse($composite->has('baz'));
    }

    public function testGet(): void
    {
        $composite = new CompositeContainer(
            new ArrayContainer(['foo' => 'foo']),
            new ArrayContainer(['bar' => 'bar']),
        );

        self::assertSame('foo', $composite->get('foo'));
        self::assertSame('bar', $composite->get('bar'));
    }

    public function testGetNotFound(): void
    {
        $composite = new CompositeContainer(
            new ArrayContainer(['foo' => 'foo']),
            new ArrayContainer(['bar' => 'bar']),
        );

        $this->expectException(NotFoundException::class);
        $composite->get('baz');
    }

    public function testFactory(): void
    {
        $container = new CompositeContainer(new FactoryContainer());

        self::assertTrue($container->hasFactory(MyClass::class));
        self::assertFalse($container->hasFactory('foo'));

        self::assertInstanceOf(MyClass::class, $container->getFactory(MyClass::class)->make());
        self::assertInstanceOf(MyClass::class, $container->make(MyClass::class));
    }

    public function testInvoker(): void
    {
        $container = new CompositeContainer(new FactoryContainer());

        self::assertSame('foo', $container->invoke([$container->make(MyClass::class), 'methodA']));
        self::assertSame('bar', $container->invoke(MyClass::class . '::staticMethodB'));
        self::assertSame(42, $container->invoke(new class {
            public function __invoke()
            {
                return 42;
            }
        }));

        self::assertSame(
            42,
            $container->invoke('\\Warp\\Container\\Fixtures\\intval', FactoryOptions::wrap([
                'value' => '42',
                'base' => 10,
            ]))
        );
    }

    public function testDefinition(): void
    {
        $container = new CompositeContainer(
            new DefinitionContainer(),
            new ArrayContainer([]), // skipped on getTagged().
        );

        $container->addServiceProvider(MyClassProvider::class);

        $container->define('foo', 'bar');
        $container->define('bar', 'baz', true);

        self::assertTrue($container->has(MyClass::class));
        self::assertTrue($container->hasTagged('tag'));
        self::assertTrue($container->has('foo'));
        self::assertTrue($container->has('bar'));

        self::assertFalse($container->hasTagged('no_tag'));
        self::assertFalse($container->has(B::class));
        self::assertFalse($container->has('baz'));

        $tagged = [...$container->getTagged('tag')];

        self::assertCount(1, $tagged);
        self::assertInstanceOf(MyClass::class, $tagged[0]);
    }

    public function testSetContainer(): void
    {
        $factory = new ReflectionFactoryAggregate();
        $invoker = new ReflectionInvoker();
        $definitionContainer = new DefinitionContainer($factory, $invoker);
        $factoryContainer = new FactoryContainer($factory, $invoker);
        $compositeContainer = new CompositeContainer($definitionContainer, $factoryContainer);

        self::assertSame($compositeContainer, $definitionContainer->getContainer());
        self::assertSame($compositeContainer, $factoryContainer->getContainer());

        $matryoshka = new CompositeContainer($compositeContainer);

        self::assertSame($matryoshka, $definitionContainer->getContainer());
        self::assertSame($matryoshka, $factoryContainer->getContainer());
    }
}
