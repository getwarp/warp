<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use spaceonfire\CommandBus\Fixtures\Command\AddTaskCommand;
use spaceonfire\CommandBus\Fixtures\Command\CompleteTaskCommand;
use spaceonfire\CommandBus\Fixtures\Handler\AddTaskCommandHandler;
use spaceonfire\CommandBus\Mapping\MapByStaticList;
use stdClass;

class CommandBusTest extends TestCase
{
    /**
     * @var ContainerInterface&MockObject
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);

        $handlerMock = $this->createMock(AddTaskCommandHandler::class);
        $handlerMock
            ->expects(self::atMost(1))
            ->method('handle')
            ->willReturn('a-return-value');

        $this->container
            ->method('get')
            ->with(AddTaskCommandHandler::class)
            ->willReturn($handlerMock);
    }

    private function commandBusFactory(array $middlewares = [], bool $mockContainer = false): CommandBus
    {
        return new CommandBus(
            new MapByStaticList([
                AddTaskCommand::class => [AddTaskCommandHandler::class, 'handle'],
                CompleteTaskCommand::class => [stdClass::class, 'handle'],
            ]),
            $middlewares,
            $mockContainer ? $this->container : null
        );
    }

    public function testCommandBusWithoutMiddleware(): void
    {
        $commandBus = $this->commandBusFactory();
        $this->assertEquals('foobar', $commandBus->handle(new AddTaskCommand()));
    }

    public function testCommandBusMiddlewareValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->commandBusFactory([new stdClass()]);
    }

    public function testAllMiddlewareAreExecutedAndReturnValuesAreRespected(): void
    {
        $executionOrder = [];

        $middleware1 = $this->createMock(Middleware::class);
        $middleware1->method('execute')->willReturnCallback(
            static function ($command, $next) use (&$executionOrder) {
                $executionOrder[] = 1;

                return $next($command);
            }
        );

        $middleware2 = $this->createMock(Middleware::class);
        $middleware2->method('execute')->willReturnCallback(
            static function ($command, $next) use (&$executionOrder) {
                $executionOrder[] = 2;

                return $next($command);
            }
        );

        $middleware3 = $this->createMock(Middleware::class);
        $middleware3->method('execute')->willReturnCallback(
            static function () use (&$executionOrder) {
                $executionOrder[] = 3;

                return 'foobar';
            }
        );

        $commandBus = $this->commandBusFactory([$middleware1, $middleware2, $middleware3]);

        self::assertEquals('foobar', $commandBus->handle(new AddTaskCommand()));
        self::assertEquals([1, 2, 3], $executionOrder);
    }

    public function testSingleMiddlewareWorks(): void
    {
        $middleware = $this->createMock(Middleware::class);
        $middleware->expects(self::once())->method('execute')->willReturn('foobar');

        $commandBus = $this->commandBusFactory([$middleware]);

        self::assertEquals('foobar', $commandBus->handle(new AddTaskCommand()));
    }

    public function testCommandBusWithContainer(): void
    {
        $commandBus = $this->commandBusFactory([], true);
        self::assertEquals('a-return-value', $commandBus->handle(new AddTaskCommand()));
    }

    public function testCommandBusCannotInvokeHandler(): void
    {
        $this->expectException(CanNotInvokeHandler::class);
        $commandBus = $this->commandBusFactory();
        $command = new CompleteTaskCommand();
        try {
            $commandBus->handle($command);
        } catch (CanNotInvokeHandler $exception) {
            $this->assertEquals($command, $exception->getCommand());
            throw $exception;
        }
    }

    public function testClone(): void
    {
        $commandBus = $this->commandBusFactory();
        $commandBusClone = clone $commandBus;
        self::assertTrue(true);
    }
}
