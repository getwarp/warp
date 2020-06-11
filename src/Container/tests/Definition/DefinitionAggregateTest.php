<?php

declare(strict_types=1);

namespace spaceonfire\Container\Definition;

use PHPUnit\Framework\TestCase;
use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\Exception\ContainerException;

class DefinitionAggregateTest extends TestCase
{
    public function testDefinitionOperations(): void
    {
        $aggregate = new DefinitionAggregate();

        $definition = $aggregate->makeDefinition('foo', 'bar', true);

        $aggregate->addDefinition($definition);

        self::assertTrue($aggregate->hasDefinition('foo'));
        self::assertFalse($aggregate->hasDefinition('baz'));

        self::assertSame($definition, $aggregate->getDefinition('foo'));
    }

    public function testGetDefinitionFailed(): void
    {
        $aggregate = new DefinitionAggregate();
        self::assertFalse($aggregate->hasDefinition('baz'));

        $this->expectException(ContainerException::class);
        $aggregate->getDefinition('baz');
    }

    public function testAddDefinitionDuplicateShouldThrowException(): void
    {
        $this->expectException(ContainerException::class);

        $aggregate = new DefinitionAggregate();

        $definitionA = $aggregate->makeDefinition('foo', 'bar', true);
        $definitionB = $aggregate->makeDefinition('foo', 'baz', false);

        $aggregate->addDefinition($definitionA);

        $this->expectException(ContainerException::class);
        $aggregate->addDefinition($definitionB);
    }

    public function testResolve(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->has('bar')->willReturn(true);
        $containerProphecy->get('bar')->willReturn('baz');
        /** @var ContainerInterface $container */
        $container = $containerProphecy->reveal();

        $aggregate = new DefinitionAggregate();
        $aggregate->addDefinition(new Definition('foo', 'bar'));

        self::assertSame('baz', $aggregate->resolve('foo', $container));
    }
}
