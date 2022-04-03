<?php

declare(strict_types=1);

namespace Warp\Container\Reflection;

use Warp\Container\AbstractTestCase;
use Warp\Container\Argument\ArgumentResolver;
use Warp\Container\ContainerInterface;
use Warp\Container\Fixtures\A;
use Warp\Container\Fixtures\B;
use Warp\Container\Fixtures\MyClass;

class ReflectionInvokerTest extends AbstractTestCase
{
    /**
     * @var ReflectionInvoker
     */
    private $invoker;

    protected function setUp(): void
    {
        parent::setUp();

        $containerProphesy = $this->createContainerMock([
            A::class => new A(),
            B::class => new B(new A()),
            MyClass::class => new MyClass(),
        ]);
        /** @var ContainerInterface $container */
        $container = $containerProphesy->reveal();

        $resolver = new ArgumentResolver($container);

        $this->invoker = new ReflectionInvoker($resolver, $container);
    }

    /**
     * @dataProvider dataProvider
     * @param mixed $expect
     * @param callable|mixed $callable
     * @param array $arguments
     */
    public function testInvoker($expect, $callable, $arguments = []): void
    {
        self::assertSame($expect, ($this->invoker)($callable, $arguments));
    }

    public function dataProvider(): array
    {
        return [
            ['bar', MyClass::class . '::staticMethod'],
            ['foo', [new MyClass(), 'method']],
            [42, new class {
                public function __invoke()
                {
                    return 42;
                }
            }],
            [42, 'intval', [
                (PHP_VERSION_ID >= 80000 ? 'value' : 'var') => '42',
                'base' => 10,
            ]],
        ];
    }
}
