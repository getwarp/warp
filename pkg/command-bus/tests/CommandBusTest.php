<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus;

use PHPUnit\Framework\TestCase;
use spaceonfire\CommandBus\Exception\CannotInvokeHandlerException;
use spaceonfire\CommandBus\Fixtures\Command\AddTaskCommand;
use spaceonfire\CommandBus\Fixtures\Command\CompleteTaskCommand;
use spaceonfire\CommandBus\Fixtures\Handler\AddTaskCommandHandler;
use spaceonfire\CommandBus\Fixtures\Handler\HandleMethodHandler;
use spaceonfire\CommandBus\Mapping\MapByStaticList;

class CommandBusTest extends TestCase
{
    public function testDefaultWithoutMiddleware(): void
    {
        $commandBus = new CommandBus(
            new MapByStaticList([
                AddTaskCommand::class => [AddTaskCommandHandler::class, 'handle'],
            ]),
        );

        self::assertSame('foobar', $commandBus->handle(new AddTaskCommand()));
    }

    public function testWithOneMiddleware(): void
    {
        $middleware = new class implements MiddlewareInterface {
            public int $times = 0;

            public function execute(object $command, callable $next)
            {
                ++$this->times;
                return $next($command);
            }
        };

        $commandBus = new CommandBus(
            new MapByStaticList([
                AddTaskCommand::class => [AddTaskCommandHandler::class, 'handle'],
            ]),
            [$middleware],
        );

        self::assertSame('foobar', $commandBus->handle(new AddTaskCommand()));
        self::assertSame(1, $middleware->times);
    }

    public function testAllMiddlewareAreExecutedInProperOrder(): void
    {
        $executionStack = new \ArrayObject();

        $firstMiddleware = new class($executionStack) implements MiddlewareInterface {
            private \ArrayObject $stack;

            public function __construct(\ArrayObject $stack)
            {
                $this->stack = $stack;
            }

            public function execute(object $command, callable $next)
            {
                $this->stack->append($this);
                return $next($command);
            }
        };

        $secondMiddleware = new class($executionStack) implements MiddlewareInterface {
            private \ArrayObject $stack;

            public function __construct(\ArrayObject $stack)
            {
                $this->stack = $stack;
            }

            public function execute(object $command, callable $next)
            {
                $this->stack->append($this);
                return $next($command);
            }
        };

        $thirdMiddleware = new class($executionStack) implements MiddlewareInterface {
            private \ArrayObject $stack;

            public function __construct(\ArrayObject $stack)
            {
                $this->stack = $stack;
            }

            public function execute(object $command, callable $next)
            {
                $this->stack->append($this);
                return $next($command);
            }
        };

        $commandBus = new CommandBus(
            new MapByStaticList([
                AddTaskCommand::class => [AddTaskCommandHandler::class, 'handle'],
            ]),
            [$firstMiddleware, $secondMiddleware, $thirdMiddleware],
        );

        self::assertSame('foobar', $commandBus->handle(new AddTaskCommand()));
        self::assertSame([$firstMiddleware, $secondMiddleware, $thirdMiddleware], $executionStack->getArrayCopy());
    }

    public function testEarlyReturnInMiddleware(): void
    {
        $executionStack = new \ArrayObject();

        $firstMiddleware = new class($executionStack) implements MiddlewareInterface {
            private \ArrayObject $stack;

            public function __construct(\ArrayObject $stack)
            {
                $this->stack = $stack;
            }

            public function execute(object $command, callable $next): int
            {
                $this->stack->append($this);

                return 42;
            }
        };

        $secondMiddleware = new class($executionStack) implements MiddlewareInterface {
            private \ArrayObject $stack;

            public function __construct(\ArrayObject $stack)
            {
                $this->stack = $stack;
            }

            public function execute(object $command, callable $next)
            {
                $this->stack->append($this);
                return $next($command);
            }
        };

        $commandBus = new CommandBus(
            new MapByStaticList([
                AddTaskCommand::class => [AddTaskCommandHandler::class, 'handle'],
            ]),
            [$firstMiddleware, $secondMiddleware],
        );

        self::assertSame(42, $commandBus->handle(new AddTaskCommand()));
        self::assertSame([$firstMiddleware], $executionStack->getArrayCopy());
    }

    public function testCommandBusMiddlewareValidation(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $commandBus = new CommandBus(
            new MapByStaticList([
                AddTaskCommand::class => [AddTaskCommandHandler::class, 'handle'],
            ]),
            [(object)[]],
        );
    }

    public function testCommandBusCannotInvokeHandler(): void
    {
        $commandBus = new CommandBus(
            new MapByStaticList([
                CompleteTaskCommand::class => [HandleMethodHandler::class, 'unknownMethod'],
            ]),
        );
        $command = new CompleteTaskCommand();
        try {
            $commandBus->handle($command);
        } catch (\Throwable $exception) {
        } finally {
            \assert(isset($exception));
            self::assertInstanceOf(CannotInvokeHandlerException::class, $exception);
            self::assertSame($command, $exception->getCommand());
            self::assertSame('Cannot invoke handler', $exception->getName());
        }
    }

    public function testClone(): void
    {
        $commandBus = new CommandBus(
            new MapByStaticList([]),
        );
        $commandBusClone = clone $commandBus;
        self::assertTrue(true);

        // TODO: test that clone command bus will rebind middlewareChain
        $this->markTestIncomplete();
    }
}
