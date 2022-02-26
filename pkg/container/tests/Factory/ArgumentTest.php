<?php

declare(strict_types=1);

namespace spaceonfire\Container\Factory;

use PhpOption\Some;
use PHPUnit\Framework\TestCase;
use spaceonfire\Container\CompositeContainer;
use spaceonfire\Container\DefinitionContainer;
use spaceonfire\Container\Exception\CannotResolveArgumentException;
use spaceonfire\Container\FactoryContainer;
use spaceonfire\Container\Fixtures\A;
use spaceonfire\Container\Fixtures\AA;
use spaceonfire\Container\Fixtures\AbstractClass\AbstractClass;
use spaceonfire\Container\Fixtures\ArrayContainer;
use spaceonfire\Container\Fixtures\B;
use spaceonfire\Container\Fixtures\MyClass;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\InstanceOfType;
use spaceonfire\Type\UnionType;

class ArgumentTest extends TestCase
{
    private function makeContainer(?array $services = null): ArrayContainer
    {
        $services ??= [
            A::class => new A(),
            B::class => new B(new A()),
        ];
        return new ArrayContainer($services);
    }

    public function testResolveFromOptions(): void
    {
        $argument = new Argument('arg', '', InstanceOfType::new(AA::class));
        $obj = new AA();
        $options = FactoryOptions::new()->addArgument('arg', $obj);

        self::assertSame($obj, $argument->resolve($options)->current());
    }

    public function testResolveFromOptionsByAlias(): void
    {
        $argument = new Argument('arg', '', InstanceOfType::new(AA::class));

        $obj = new AA();
        $container = $this->makeContainer([
            'alias' => $obj,
        ]);

        $options = FactoryOptions::new();
        $options->setArgumentAlias('arg', 'alias');

        $argument->setContainer($container);

        self::assertSame($obj, $argument->resolve($options)->current());
    }

    public function testResolveByType(): void
    {
        $argument = new Argument('arg', '', InstanceOfType::new(B::class));

        $argument->setContainer($this->makeContainer());

        self::assertInstanceOf(B::class, $argument->resolve()->current());
    }

    public function testResolveByDisjunctionType(): void
    {
        $container = $this->makeContainer();

        $argument = new Argument('arg', '', UnionType::new(
            InstanceOfType::new(A::class),
            InstanceOfType::new(B::class),
            InstanceOfType::new(MyClass::class),
        ));
        $argument->setContainer($container);
        self::assertInstanceOf(A::class, $argument->resolve()->current());

        $argument = new Argument('arg', '', UnionType::new(
            InstanceOfType::new(B::class),
            InstanceOfType::new(MyClass::class),
        ));
        $argument->setContainer($container);
        self::assertInstanceOf(B::class, $argument->resolve()->current());

        $argument = new Argument('arg', '', UnionType::new(
            InstanceOfType::new(MyClass::class),
            InstanceOfType::new(B::class),
        ));
        $argument->setContainer($container);
        self::assertInstanceOf(B::class, $argument->resolve()->current());
    }

    /**
     * @dataProvider optionalArguments
     * @param Argument $argument
     * @param mixed $expected
     * @param array|null $containerServices
     */
    public function testResolveDefault(Argument $argument, $expected, ?array $containerServices = null): void
    {
        $container = $this->makeContainer($containerServices);
        $argument->setContainer($container);
        $resolved = [...$argument->resolve()];
        self::assertSame($expected, $resolved);
    }

    public function optionalArguments(): \Generator
    {
        $obj = new AA();
        yield [
            new Argument('', '', InstanceOfType::new(AA::class), new Some($obj)),
            [$obj],
        ];

        yield [
            new Argument('', '', UnionType::new(InstanceOfType::new(MyClass::class), BuiltinType::null())),
            [null],
        ];

        yield [
            new Argument('', '', null, null, true),
            [],
        ];
    }

    public function testCannotResolve(): void
    {
        $argument = new Argument('arg', '', InstanceOfType::new(MyClass::class));

        $argument->setContainer($this->makeContainer());

        $this->expectException(CannotResolveArgumentException::class);
        $argument->resolve()->current();
    }

    public function testCannotResolveWithoutContainer(): void
    {
        $argument = new Argument('arg', '', InstanceOfType::new(MyClass::class));

        $this->expectException(CannotResolveArgumentException::class);
        $argument->resolve()->current();
    }

    public function testResolveVariadicByTag(): void
    {
        $argument = new Argument('arg', '', InstanceOfType::new(A::class), null, true);

        $container = new DefinitionContainer();

        $container->define(A::class)->addTag(A::class);
        $container->define(AA::class)->addTag(A::class);

        $argument->setContainer($container);

        $resolved = [...$argument->resolve()];

        self::assertCount(2, $resolved);
    }

    public function testResolveVariadicFromOptionsByTag(): void
    {
        $argument = new Argument('arg', '', null, null, true);

        $container = new DefinitionContainer();

        $container->define(A::class)->addTag('tag');
        $container->define(B::class)->addTag('tag');

        $options = FactoryOptions::new();
        $options->setArgumentTag('arg', 'tag');

        $argument->setContainer($container);

        $resolved = [...$argument->resolve($options)];

        self::assertCount(2, $resolved);
    }

    public function testResolveVariadicByTagSkipNoDefinitionContainer(): void
    {
        $argument = new Argument('arg', '', null, null, true);

        $options = FactoryOptions::new();
        $options->setArgumentTag('arg', 'tag');

        $argument->setContainer($this->makeContainer());

        $resolved = [...$argument->resolve($options)];

        self::assertCount(0, $resolved);
    }

    public function testResolveVariadicByTagSkipHasNotTag(): void
    {
        $argument = new Argument('arg', '', InstanceOfType::new(A::class), null, true);

        $container = new DefinitionContainer();

        $container->define(A::class);
        $container->define(B::class);

        $options = FactoryOptions::new();
        $options->setArgumentTag('arg', 'tag');

        $argument->setContainer($container);

        $resolved = [...$argument->resolve($options)];

        self::assertCount(1, $resolved);
    }

    public function testCannotResolveVariadicWithoutContainer(): void
    {
        $argument = new Argument('arg', '', null, null, true);

        $options = FactoryOptions::new();
        $options->setArgumentTag('arg', 'tag');

        $this->expectException(CannotResolveArgumentException::class);
        $argument->resolve($options)->current();
    }

    public function testResolveAbstractClass(): void
    {
        $argument = new Argument('arg', '', InstanceOfType::new(AbstractClass::class));
        $argument->setContainer(new FactoryContainer());

        $this->expectException(CannotResolveArgumentException::class);
        $argument->resolve()->current();
    }

    public function testResolveAbstractClassVariadic(): void
    {
        $container = new CompositeContainer(
            new DefinitionContainer(),
            new FactoryContainer(),
        );
        $container->define(AbstractClass::class)->addTag(AbstractClass::class);

        $argument = new Argument('arg', '', InstanceOfType::new(AbstractClass::class), null, true);
        $argument->setContainer($container);

        $resolved = [...$argument->resolve()];

        self::assertCount(0, $resolved);
    }
}
