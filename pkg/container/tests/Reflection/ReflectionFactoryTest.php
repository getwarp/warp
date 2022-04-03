<?php

declare(strict_types=1);

namespace Warp\Container\Reflection;

use Warp\Container\AbstractTestCase;
use Warp\Container\Argument\ArgumentResolver;
use Warp\Container\ContainerInterface;
use Warp\Container\Exception\NotFoundException;
use Warp\Container\Fixtures\A;
use Warp\Container\Fixtures\B;

class ReflectionFactoryTest extends AbstractTestCase
{
    /**
     * @var ReflectionFactory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $containerProphesy = $this->createContainerMock([
            A::class => new A(),
            B::class => new B(new A()),
        ]);
        /** @var ContainerInterface $container */
        $container = $containerProphesy->reveal();

        $resolver = new ArgumentResolver($container);

        $this->factory = new ReflectionFactory($resolver);
    }

    /**
     * @dataProvider factoryDataProvider
     * @param string $className
     * @param array $arguments
     */
    public function testFactory(string $className, array $arguments = []): void
    {
        self::assertInstanceOf($className, ($this->factory)($className, $arguments));
    }

    public function factoryDataProvider(): array
    {
        return [
            [A::class],
            [B::class],
            [B::class, ['a' => new A()]],
        ];
    }

    public function testMakeNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        ($this->factory)('something_that_dont_exist');
    }
}
