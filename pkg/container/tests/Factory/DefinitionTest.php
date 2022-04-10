<?php

declare(strict_types=1);

namespace Warp\Container\Factory;

use PhpOption\Some;
use PHPUnit\Framework\TestCase;
use Warp\Container\Exception\ContainerException;
use Warp\Container\FactoryAggregateInterface;
use Warp\Container\FactoryInterface;
use Warp\Container\FactoryOptionsInterface;
use Warp\Container\Fixtures\A;
use Warp\Container\Fixtures\AA;
use Warp\Container\Fixtures\ArrayContainer;
use Warp\Container\Fixtures\ArrayFactoryAggregate;
use Warp\Container\Fixtures\B;
use Warp\Container\InvokerInterface;

class DefinitionTest extends TestCase
{
    public function testDefaultDefinition(): void
    {
        $def = new Definition(A::class);

        self::assertSame(A::class, $def->getId());

        $container = new ArrayContainer([
            FactoryAggregateInterface::class => new ArrayFactoryAggregate([
                A::class => new class implements FactoryInterface {
                    public function make(?FactoryOptionsInterface $options = null)
                    {
                        return new A();
                    }
                },
            ]),
        ]);

        $def->setContainer($container);

        $obj = $def->make();

        self::assertInstanceOf(A::class, $obj);
    }

    public function testDefinitionShared(): void
    {
        $def = new Definition(A::class, null, true);

        $container = new ArrayContainer([
            FactoryAggregateInterface::class => new ArrayFactoryAggregate([
                A::class => new class implements FactoryInterface {
                    public function make(?FactoryOptionsInterface $options = null)
                    {
                        return new A();
                    }
                },
            ]),
        ]);

        $def->setContainer($container);

        $obj = $def->make();
        $obj2 = $def->make();

        self::assertInstanceOf(A::class, $obj);
        self::assertSame($obj, $obj2);
    }

    public function testDefinitionAlias(): void
    {
        $def = new Definition(A::class, AA::class);

        $container = new ArrayContainer([
            AA::class => new AA(),
        ]);

        $def->setContainer($container);

        $obj = $def->make();

        self::assertInstanceOf(AA::class, $obj);
    }

    public function testDefinitionCallable(): void
    {
        $def = new Definition(A::class, static fn () => new A());

        $container = new ArrayContainer([
            InvokerInterface::class => new class implements InvokerInterface {
                public function invoke(callable $callable, $options = null)
                {
                    return $callable();
                }
            },
        ]);

        $def->setContainer($container);

        $obj = $def->make();

        self::assertInstanceOf(A::class, $obj);
    }

    public function testDefinitionPredefined(): void
    {
        $def = new Definition(A::class, new A());

        $obj = $def->make();

        self::assertInstanceOf(A::class, $obj);
    }

    public function testDefinitionPredefinedNonObject(): void
    {
        $def = new Definition('foo', new Some('bar'));

        self::assertSame('bar', $def->make());
    }

    public function testDefinitionTags(): void
    {
        $def = new Definition(A::class);

        self::assertFalse($def->hasTag('tag1'));
        self::assertSame([], $def->getTags());

        $def->addTag('tag1');
        $def->addTag('tag2');

        self::assertTrue($def->hasTag('tag1'));
        self::assertTrue($def->hasTag('tag2'));
        self::assertFalse($def->hasTag('tag3'));
        self::assertSame(['tag1', 'tag2'], $def->getTags());
    }

    public function testDefinitionImplementsFactoryOptionsInterface(): void
    {
        $def = new Definition(B::class);

        self::assertFalse($def->hasArgument('a'));

        $def->addArgument('a', $arg = new A());

        self::assertTrue($def->hasArgument('a'));
        self::assertSame($arg, $def->getArgument('a'));

        self::assertNull($def->getStaticConstructor());
        $def->setStaticConstructor('new');
        self::assertSame('new', $def->getStaticConstructor());

        self::assertNull($def->getArgumentAlias('a'));
        $def->setArgumentAlias('a', 'alias');
        self::assertSame('alias', $def->getArgumentAlias('a'));

        self::assertNull($def->getArgumentTag('a'));
        $def->setArgumentTag('a', 'tag');
        self::assertSame('tag', $def->getArgumentTag('a'));

        self::assertCount(0, $def->getMethodCalls());
        $def->addMethodCall('method');
        self::assertCount(1, $def->getMethodCalls());
    }

    public function testNoContainerException(): void
    {
        $def = new Definition(A::class);
        $this->expectException(ContainerException::class);
        $def->make();
    }

    public function testNotResolvableDefinition(): void
    {
        $def = new Definition(A::class, AA::class);

        $def->setContainer(new ArrayContainer([]));

        $this->expectException(ContainerException::class);
        $def->make();
    }
}
