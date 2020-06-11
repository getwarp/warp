<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use spaceonfire\Container\Argument\Argument;
use spaceonfire\Container\Argument\ArgumentValue;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\Exception\NotFoundException;
use spaceonfire\Container\Fixtures\A;
use spaceonfire\Container\Fixtures\B;
use spaceonfire\Container\Fixtures\BadServiceProvider;
use spaceonfire\Container\Fixtures\InvalidDependencyClass;
use spaceonfire\Container\Fixtures\MyClass;
use spaceonfire\Container\Fixtures\MyClassProvider;

class ContainerTest extends TestCase
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

        self::assertInstanceOf(B::class, $container->get(B::class));

        $container->addServiceProvider(BadServiceProvider::class);

        $this->expectException(ContainerException::class);
        $container->get('bad');
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
        self::assertTrue($container->has(B::class));
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

        self::assertSame('bar', $container->invoke(MyClass::class . '::staticMethod'));

        $oldReporting = error_reporting(E_ALL ^ E_DEPRECATED);
        self::assertSame('foo', $container->invoke([MyClass::class, 'method']));
        error_reporting($oldReporting);

        self::assertSame('foo', $container->invoke([new MyClass(), 'method']));

        $invokable = new class {
            public function __invoke()
            {
                return 42;
            }
        };
        self::assertSame(42, $container->invoke($invokable));

        self::assertSame(42, $container->invoke('intval', ['var' => '42', 'base' => 10]));
    }

    /**
     * @dataProvider resolveArgumentsDataProvider
     * @param array $arguments
     */
    public function testResolveArguments(array $arguments = []): void
    {
        $method = new ReflectionMethod(MyClass::class, 'methodForResolve');

        $container = new Container();

        $resolved = $container->resolveArguments($method, $arguments);

        self::assertCount(3, $resolved);
        self::assertInstanceOf(B::class, $resolved[0]);
        self::assertInstanceOf(A::class, $resolved[1]);
        self::assertIsInt($resolved[2]);
    }

    public function resolveArgumentsDataProvider(): array
    {
        return [
            [[]],
            [['int' => 23]],
            [['b' => new Argument('b', B::class)]],
            [['a' => new Argument('a', A::class, new ArgumentValue(new A()))]],
        ];
    }

    public function testResolveArgumentsFailed(): void
    {
        $this->expectException(ContainerException::class);

        $method = new ReflectionMethod(MyClass::class, 'methodForResolve');

        $container = new Container();

        $container->resolveArguments($method, ['b' => new Argument('b', 'something_that_dont_exist')]);
    }
}
