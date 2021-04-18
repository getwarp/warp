<?php

declare(strict_types=1);

namespace spaceonfire\Container\Argument;

use ReflectionMethod;
use spaceonfire\Container\AbstractTestCase;
use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\Fixtures\A;
use spaceonfire\Container\Fixtures\B;
use spaceonfire\Container\Fixtures\MyClass;
use spaceonfire\Container\RawValueHolder;

class ArgumentResolverTest extends AbstractTestCase
{
    /**
     * @var ArgumentResolver
     */
    private $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $containerProphesy = $this->createContainerMock([
            A::class => new A(),
            B::class => new B(new A()),
        ]);
        /** @var ContainerInterface $container */
        $container = $containerProphesy->reveal();

        $this->resolver = new ArgumentResolver($container);
    }

    /**
     * @dataProvider resolveArgumentsDataProvider
     * @param array $arguments
     */
    public function testResolveArguments(array $arguments = []): void
    {
        $method = new ReflectionMethod(MyClass::class, 'methodForResolve');

        $resolvedArguments = $this->resolver->resolveArguments($method, $arguments);

        self::assertCount(3, $resolvedArguments);
        self::assertInstanceOf(B::class, $resolvedArguments[0]);
        self::assertInstanceOf(A::class, $resolvedArguments[1]);
        self::assertIsInt($resolvedArguments[2]);
    }

    public function resolveArgumentsDataProvider(): array
    {
        return [
            [[]],
            [['int' => 23]],
            [['b' => new Argument('b', B::class)]],
            [['a' => new Argument('a', A::class, new RawValueHolder(new A()))]],
        ];
    }

    public function testResolveArgumentsFailed(): void
    {
        $this->expectException(ContainerException::class);

        $method = new ReflectionMethod(MyClass::class, 'methodForResolve');

        $this->resolver->resolveArguments($method, ['b' => new Argument('b', 'something_that_dont_exist')]);
    }
}
