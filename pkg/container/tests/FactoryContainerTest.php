<?php

declare(strict_types=1);

namespace Warp\Container;

use PHPUnit\Framework\TestCase;
use Warp\Container\Exception\CannotResolveArgumentException;
use Warp\Container\Exception\ContainerException;
use Warp\Container\Exception\NotFoundException;
use Warp\Container\Factory\FactoryOptions;
use Warp\Container\Fixtures\A;
use Warp\Container\Fixtures\AbstractClass\AcceptNullableAbstractClass;
use Warp\Container\Fixtures\AbstractClass\RequiresAbstractClass;
use Warp\Container\Fixtures\B;
use Warp\Container\Fixtures\MethodsCallFixture;
use Warp\Container\Fixtures\MyClass;
use Warp\Container\Fixtures\StaticConstructorClass;

class FactoryContainerTest extends TestCase
{
    public function testHas(): void
    {
        $container = new FactoryContainer();
        self::assertTrue($container->has(A::class));
        self::assertTrue($container->has(B::class));
        self::assertTrue($container->has(MyClass::class));
        self::assertFalse($container->has('something_that_dont_exist'));
    }

    public function testGet(): void
    {
        $container = new FactoryContainer();
        self::assertInstanceOf(B::class, $container->get(B::class));
    }

    public function testGetNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $container = new FactoryContainer();
        $container->get('something_that_dont_exist');
    }

    public function testFactory(): void
    {
        $container = new FactoryContainer();

        self::assertTrue($container->hasFactory(MyClass::class));
        self::assertFalse($container->hasFactory('foo'));

        self::assertInstanceOf(MyClass::class, $container->getFactory(MyClass::class)->make());
        self::assertInstanceOf(MyClass::class, $container->make(MyClass::class));
    }

    public function testFactoryStaticConstructor(): void
    {
        $container = new FactoryContainer();

        $result = $container->make(StaticConstructorClass::class);
        self::assertInstanceOf(MyClass::class, $result->getDependency());

        $options = (FactoryOptions::new())->setStaticConstructor('empty');
        $result = $container->make(StaticConstructorClass::class, $options);
        self::assertNull($result->getDependency());
    }

    public function testFactoryStaticConstructorMethodDoesNotExists(): void
    {
        $container = new FactoryContainer();

        $options = (FactoryOptions::new())->setStaticConstructor('method_does_not_exists');

        $this->expectException(ContainerException::class);
        $container->make(StaticConstructorClass::class, $options);
    }

    public function testFactoryStaticConstructorMethodNotStatic(): void
    {
        $container = new FactoryContainer();

        $options = (FactoryOptions::new())->setStaticConstructor('getDependency');

        $this->expectException(ContainerException::class);
        $container->make(StaticConstructorClass::class, $options);
    }

    public function testFactoryStaticConstructorMethodReturnsOther(): void
    {
        $container = new FactoryContainer();

        $options = (FactoryOptions::new())->setStaticConstructor('notConstructor');

        $this->expectException(ContainerException::class);
        $container->make(StaticConstructorClass::class, $options);
    }

    public function testFactoryMethodCalls(): void
    {
        $container = new FactoryContainer();

        $options = (FactoryOptions::new())
            ->addMethodCall('setName', (FactoryOptions::new())->addArgument('name', 'John Doe'))
            ->addMethodCall('withColor', (FactoryOptions::new())->addArgument('color', 'red'));

        $instance = $container->make(MethodsCallFixture::class, $options);

        self::assertInstanceOf(MethodsCallFixture::class, $instance);
        self::assertSame('John Doe', $instance->name);
        self::assertSame('red', $instance->color);
    }

    public function testInvoker(): void
    {
        $container = new FactoryContainer();

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

    public function testGetAcceptNullableAbstractClass(): void
    {
        $container = new FactoryContainer();
        $object = $container->get(AcceptNullableAbstractClass::class);
        self::assertNull($object->getAbstractClass());
    }

    public function testGetRequiresAbstractClass(): void
    {
        $this->expectException(CannotResolveArgumentException::class);
        $container = new FactoryContainer();
        $container->get(RequiresAbstractClass::class);
    }
}
