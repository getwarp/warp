<?php

declare(strict_types=1);

namespace spaceonfire\Container\Reflection;

use PHPUnit\Framework\TestCase;
use spaceonfire\Container\Argument\ArgumentResolver;
use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\Fixtures\A;
use spaceonfire\Container\Fixtures\B;
use spaceonfire\Container\Fixtures\MyClass;
use spaceonfire\Container\WithContainerMockTrait;

class ReflectionInvokerTest extends TestCase
{
    use WithContainerMockTrait;

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
        $oldReporting = error_reporting(E_ALL ^ E_DEPRECATED);
        self::assertSame($expect, ($this->invoker)($callable, $arguments));
        error_reporting($oldReporting);
    }

    public function dataProvider(): array
    {
        return [
            ['bar', MyClass::class . '::staticMethod'],
            ['foo', [MyClass::class, 'method']],
            ['foo', [new MyClass(), 'method']],
            [42, new class {
                public function __invoke()
                {
                    return 42;
                }
            }],
            [42, 'intval', ['var' => '42', 'base' => 10]],
        ];
    }
}
