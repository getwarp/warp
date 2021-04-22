<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use PHPUnit\Framework\TestCase;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\Exception\NotFoundException;
use spaceonfire\Container\Factory\FactoryOptions;
use spaceonfire\Container\Fixtures\A;
use spaceonfire\Container\Fixtures\B;
use spaceonfire\Container\Fixtures\MyClass;
use spaceonfire\Container\Fixtures\ServiceProvider\BadServiceProvider;
use spaceonfire\Container\Fixtures\ServiceProvider\MyClassProvider;
use spaceonfire\Container\Fixtures\ServiceProvider\NotAProvider;
use spaceonfire\Container\ServiceProvider\AbstractServiceProvider;
use spaceonfire\Container\ServiceProvider\BootableServiceProviderInterface;

class DefinitionContainerTest extends TestCase
{
    public function testDefine(): void
    {
        $container = new DefinitionContainer();

        $container->define('foo', 'bar');
        $container->define('bar', 'baz', true)->addTag('tag');

        self::assertTrue($container->has('foo'));
        self::assertTrue($container->has('bar'));

        self::assertTrue($container->hasTagged('tag'));
        // second call cached
        self::assertTrue($container->hasTagged('tag'));

        self::assertFalse($container->hasTagged('no_tag'));

        // No definition for any class
        self::assertFalse($container->has(B::class));

        // No definition with unknown alias
        self::assertFalse($container->has('baz'));
    }

    public function testDefineAlreadyDefined(): void
    {
        $container = new DefinitionContainer();

        $container->define('foo', 'bar');

        $this->expectException(ContainerException::class);
        $container->define('foo', 'bar');
    }

    public function testServiceProvider(): void
    {
        $container = new DefinitionContainer();

        $container->addServiceProvider(BadServiceProvider::class);
        $container->addServiceProvider(MyClassProvider::class);
        $container->addServiceProvider($bootable = new class extends AbstractServiceProvider implements BootableServiceProviderInterface {
            public bool $booted = false;

            public function boot(): void
            {
                $this->booted = true;
            }

            public function provides(): array
            {
                return ['foo'];
            }

            public function register(): void
            {
                $this->getContainer()->define('foo', MyClass::class);
            }
        });

        // from BadServiceProvider
        self::assertTrue($container->has('bad'));

        // from MyClassProvider
        self::assertTrue($container->has(MyClass::class));
        self::assertTrue($container->hasTagged('tag'));

        // from bootable provider
        self::assertTrue($container->has('foo'));
        self::assertTrue($bootable->booted);
    }

    public function testAddServiceProviderError(): void
    {
        $container = new DefinitionContainer();

        $this->expectException(ContainerException::class);
        $container->addServiceProvider(NotAProvider::class);
    }

    public function testGet(): void
    {
        $container = new DefinitionContainer();

        $container->addServiceProvider(MyClassProvider::class);

        self::assertInstanceOf(MyClass::class, $container->get(MyClass::class));
    }

    public function testGetNotFound(): void
    {
        $container = new DefinitionContainer();

        $container->addServiceProvider(BadServiceProvider::class);

        $this->expectException(NotFoundException::class);
        $container->get('bad');
    }

    public function testFactory(): void
    {
        $container = new DefinitionContainer();

        self::assertTrue($container->hasFactory(MyClass::class));
        self::assertFalse($container->hasFactory('foo'));

        self::assertInstanceOf(MyClass::class, $container->getFactory(MyClass::class)->make());
        self::assertInstanceOf(MyClass::class, $container->make(MyClass::class));
    }

    public function testInvoker(): void
    {
        $container = new DefinitionContainer();

        $container->define(A::class);
        $container->define(B::class);

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
            $container->invoke('\\spaceonfire\\Container\\Fixtures\\intval', FactoryOptions::wrap([
                'value' => '42',
                'base' => 10,
            ]))
        );
    }

    public function testGetTagged(): void
    {
        $container = new DefinitionContainer();

        $container->define(A::class);

        self::assertCount(0, $container->getTagged('tag'));

        $container->addServiceProvider(MyClassProvider::class);

        $tagged = [...$container->getTagged('tag')];

        self::assertCount(1, $tagged);
        self::assertInstanceOf(MyClass::class, $tagged[0]);
    }
}
