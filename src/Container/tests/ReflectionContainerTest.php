<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use spaceonfire\Container\Exception\NotFoundException;
use spaceonfire\Container\Fixtures\A;
use spaceonfire\Container\Fixtures\B;
use spaceonfire\Container\Fixtures\MyClass;

class ReflectionContainerTest extends TestCase
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
}
