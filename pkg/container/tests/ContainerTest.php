<?php

declare(strict_types=1);

namespace Warp\Container;

use InvalidArgumentException;
use Warp\Container\Exception\ContainerException;
use Warp\Container\Exception\NotFoundException;
use Warp\Container\Fixtures\A;
use Warp\Container\Fixtures\B;
use Warp\Container\Fixtures\BadServiceProvider;
use Warp\Container\Fixtures\MyClass;
use Warp\Container\Fixtures\MyClassProvider;

class ContainerTest extends AbstractTestCase
{
    public function testAdd(): void
    {
        $container = new Container();
        $def = $container->add('foo', 'bar');
        self::assertTrue($container->has('foo'));
        self::assertFalse($def->isShared());
    }

    public function testShare(): void
    {
        $container = new Container();
        $def = $container->share('foo', 'bar');
        self::assertTrue($container->has('foo'));
        self::assertTrue($def->isShared());
    }

    public function testGet(): void
    {
        $container = new Container();
        $container->add('foo', $result = new A());
        self::assertSame($result, $container->get('foo'));

        $container->addServiceProvider(MyClassProvider::class);
        self::assertInstanceOf(MyClass::class, $container->get(MyClass::class));

        $container->addServiceProvider(BadServiceProvider::class);

        $this->expectException(ContainerException::class);
        $container->get('bad');
    }

    public function testGetNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $container = new Container();
        $container->get(B::class);
    }

    public function testHas(): void
    {
        $container = new Container();
        $container->add('foo');
        $container->addServiceProvider(MyClassProvider::class);
        $container->addServiceProvider(BadServiceProvider::class);

        self::assertTrue($container->has('foo'));
        self::assertTrue($container->has('bad'));
        self::assertTrue($container->has(MyClass::class));
        self::assertFalse($container->has(B::class));
        self::assertFalse($container->has('bar'));
    }

    public function testAddServiceProvider(): void
    {
        $container = new Container();
        $container->addServiceProvider(MyClassProvider::class);

        $serviceProvider = new MyClassProvider();
        $serviceProvider->setIdentifier('foo');
        $container->addServiceProvider($serviceProvider);

        $this->expectException(InvalidArgumentException::class);
        $container->addServiceProvider(null);
    }

    public function testMake(): void
    {
        $container = new Container();

        self::assertInstanceOf(B::class, $container->make(B::class, ['a' => new A()]));
    }

    public function testMakeNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $container = new Container();
        $container->make('something_that_dont_exist');
    }

    public function testInvoke(): void
    {
        $container = new Container();
        $container->share(A::class);
        $container->share(B::class);
        $container->add(MyClass::class);

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

    public function testResolveDefinitionWithParentContainer(): void
    {
        $container = new CompositeContainer([
            new Container(),
            new ReflectionContainer(),
        ]);

        $container->add('foo', B::class);

        self::assertInstanceOf(B::class, $container->get('foo'));
    }

    public function testHasTagged(): void
    {
        $container = new Container();

        self::assertFalse($container->hasTagged('tag'));

        $container->add('foo')->addTag('tag');

        self::assertTrue($container->hasTagged('tag'));
    }

    public function testGetTagged(): void
    {
        $container = new Container();

        self::assertTrue($container->getTagged('tag')->isEmpty());

        $container->addServiceProvider(MyClassProvider::class);

        $container->add('foo', function () {
            return 'foo';
        })->addTag('tag');

        $resolved = $container->getTagged('tag');

        self::assertFalse($resolved->isEmpty());
        self::assertSame('foo', $resolved->first());
    }
}
