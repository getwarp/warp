<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping;

use PHPUnit\Framework\TestCase;
use spaceonfire\CommandBus\Exception\FailedToMapCommandException;
use spaceonfire\CommandBus\Fixtures\Command\AddTaskCommand;
use spaceonfire\CommandBus\Fixtures\Command\CompleteTaskCommand;
use spaceonfire\CommandBus\Fixtures\Handler\AddTaskCommandHandler;

class MappingByStaticListTest extends TestCase
{
    public function testSuccessfulMapping(): void
    {
        $mapping = new MapByStaticList([
            AddTaskCommand::class => [AddTaskCommandHandler::class, 'handle'],
        ]);

        self::assertSame(
            AddTaskCommandHandler::class,
            $mapping->getClassName(AddTaskCommand::class)
        );
        self::assertSame('handle', $mapping->getMethodName(AddTaskCommand::class));
    }

    public function testFailedClassNameMapping(): void
    {
        $mapping = new MapByStaticList([
            AddTaskCommand::class => [AddTaskCommandHandler::class, 'handle'],
        ]);

        $this->expectExceptionObject(FailedToMapCommandException::className(CompleteTaskCommand::class));
        $mapping->getClassName(CompleteTaskCommand::class);
    }

    public function testFailedMethodNameMapping(): void
    {
        $mapping = new MapByStaticList([
            AddTaskCommand::class => [AddTaskCommandHandler::class, 'handle'],
        ]);

        $this->expectExceptionObject(FailedToMapCommandException::methodName(CompleteTaskCommand::class));
        $mapping->getMethodName(CompleteTaskCommand::class);
    }
}
