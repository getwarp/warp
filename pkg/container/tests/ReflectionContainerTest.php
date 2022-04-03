<?php

declare(strict_types=1);

namespace Warp\Container;

use BadMethodCallException;
use Warp\Container\Exception\ContainerException;
use Warp\Container\Exception\NotFoundException;
use Warp\Container\Fixtures\A;
use Warp\Container\Fixtures\AbstractClass\AcceptNullableAbstractClass;
use Warp\Container\Fixtures\AbstractClass\RequiresAbstractClass;
use Warp\Container\Fixtures\B;
use Warp\Container\Fixtures\MyClass;

class ReflectionContainerTest extends AbstractTestCase
{
    public function testHas(): void
    {
        $container = new ReflectionContainer();
        self::assertTrue($container->has(A::class));
        self::assertTrue($container->has(B::class));
        self::assertTrue($container->has(MyClass::class));
        self::assertFalse($container->has('something_that_dont_exist'));
    }

    public function testGet(): void
    {
        $container = new ReflectionContainer();
        self::assertInstanceOf(B::class, $container->get(B::class));
    }

    public function testGetNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $container = new Container();
        $container->get('something_that_dont_exist');
    }

    public function testInvoke(): void
    {
        $container = new ReflectionContainer();

        self::assertSame('bar', $container->invoke(MyClass::class . '::staticMethod'));

        self::assertSame('foo', $container->invoke([new MyClass(), 'method']));

        $invokable = new class {
            public function __invoke()
            {
                return 42;
            }
        };
        self::assertSame(42, $container->invoke($invokable));

        self::assertSame(42, $container->invoke('intval', [
            (PHP_VERSION_ID >= 80000 ? 'value' : 'var') => '42',
            'base' => 10,
        ]));
    }

    public function testAdd(): void
    {
        $this->expectException(BadMethodCallException::class);
        $container = new ReflectionContainer();
        $container->add('foo', 'bar');
    }

    public function testShare(): void
    {
        $this->expectException(BadMethodCallException::class);
        $container = new ReflectionContainer();
        $container->share('foo', 'bar');
    }

    public function testHasTagged(): void
    {
        $container = new ReflectionContainer();
        self::assertFalse($container->hasTagged('tag'));
    }

    public function testGetTagged(): void
    {
        $container = new ReflectionContainer();
        self::assertTrue($container->getTagged('tag')->isEmpty());
    }

    public function testGetAcceptNullableAbstractClass(): void
    {
        $container = new ReflectionContainer();
        $object = $container->get(AcceptNullableAbstractClass::class);
        self::assertNull($object->getAbstractClass());
    }

    public function testGetRequiresAbstractClass(): void
    {
        $this->expectException(ContainerException::class);
        $container = new ReflectionContainer();
        $container->get(RequiresAbstractClass::class);
    }
}
