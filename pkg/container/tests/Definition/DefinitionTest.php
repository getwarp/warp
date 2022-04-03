<?php

declare(strict_types=1);

namespace Warp\Container\Definition;

use Prophecy\Argument as ArgumentProphecy;
use Warp\Container\AbstractTestCase;
use Warp\Container\Argument\Argument;
use Warp\Container\ContainerInterface;
use Warp\Container\Exception\ContainerException;
use Warp\Container\RawValueHolder;
use stdClass;

class DefinitionTest extends AbstractTestCase
{

    public function testGetters(): void
    {
        $foo = new Definition('foo');
        self::assertSame('foo', $foo->getAbstract());
        self::assertSame('foo', $foo->getConcrete());
        self::assertFalse($foo->isShared());

        $bar = new Definition('foo', 'bar', true);
        self::assertSame('foo', $bar->getAbstract());
        self::assertSame('bar', $bar->getConcrete());
        self::assertTrue($bar->isShared());

        $object = new Definition('foo', $objectConcrete = new stdClass());
        self::assertSame('foo', $object->getAbstract());
        self::assertSame($objectConcrete, $object->getConcrete());
        self::assertTrue($object->isShared());

        $closure = new Definition('foo', $closureConcrete = static function () {
            return 'bar';
        });
        self::assertSame('foo', $closure->getAbstract());
        self::assertSame($closureConcrete, $closure->getConcrete());
        self::assertFalse($closure->isShared());
    }

    public function testResolveSharedObject(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $definition = new Definition('foo', $result = new stdClass());

        self::assertSame($result, $definition->resolve($container));
        // Second call should return cached value
        self::assertSame($result, $definition->resolve($container));
    }

    public function testAddArguments(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->invoke(ArgumentProphecy::type('callable'), ArgumentProphecy::type('iterable'))
            ->will(function ($args) {
                return call_user_func_array($args[0], $args[1]);
            });

        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $definition = new Definition('foo', static function (Argument $arg) use ($container) {
            return $arg->resolve($container);
        });

        $definition->addArguments([new Argument('arg', null, new RawValueHolder('bar'))]);

        self::assertSame('bar', $definition->resolve($container));
    }

    public function testAddMethodCalls(): void
    {
        $concrete = new class {
            public $patchedTimes = 0;

            public function patch(): void
            {
                $this->patchedTimes++;
            }
        };

        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->invoke(ArgumentProphecy::type('callable'), ArgumentProphecy::type('iterable'))
            ->will(function ($args) {
                return call_user_func_array($args[0], $args[1]);
            });
        $containerProphecy->has('bar')->willReturn(true);
        $containerProphecy->get('bar')->willReturn($concrete);
        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $definition = new Definition('foo', 'bar');
        $definition->addMethodCalls([
            'patch' => [],
        ]);

        self::assertSame($concrete, $definition->resolve($container));
        self::assertSame(1, $concrete->patchedTimes);
    }

    public function testResolveBuildAnyway(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->make('foo', ArgumentProphecy::type('iterable'))->willReturn('bar');
        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $definition = new Definition('foo');

        self::assertSame('bar', $definition->resolve($container));
    }

    public function testResolveFailed(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->has('bar')->willReturn(true);
        $containerProphecy->get('bar')->willReturn(null);
        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $definition = new Definition('foo', 'bar');

        $this->expectException(ContainerException::class);

        $definition->resolve($container);
    }

    public function testResolveUsingRawValueHolder(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $definition = new Definition('foo', new RawValueHolder('foo'));

        $resolved = $definition->resolve($container);

        self::assertSame('foo', $resolved);
    }

    public function testTags(): void
    {
        $definition = new Definition('foo', 'bar');

        self::assertFalse($definition->hasTag('baz'));

        $definition->addTag('baz');

        self::assertTrue($definition->hasTag('baz'));

        self::assertSame(['baz'], $definition->getTags());
    }
}
