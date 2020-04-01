<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping;

use PHPUnit\Framework\TestCase;
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

        static::assertEquals(
            AddTaskCommandHandler::class,
            $mapping->getClassName(AddTaskCommand::class)
        );
        static::assertEquals('handle', $mapping->getMethodName(AddTaskCommand::class));
    }

    public function testFailedClassNameMapping(): void
    {
        $mapping = new MapByStaticList([
            AddTaskCommand::class => [AddTaskCommandHandler::class, 'handle'],
        ]);

        $this->expectExceptionObject(FailedToMapCommand::className(CompleteTaskCommand::class));
        $mapping->getClassName(CompleteTaskCommand::class);
    }

    public function testFailedMethodNameMapping(): void
    {
        $mapping = new MapByStaticList([
            AddTaskCommand::class => [AddTaskCommandHandler::class, 'handle'],
        ]);

        $this->expectExceptionObject(FailedToMapCommand::methodName(CompleteTaskCommand::class));
        $mapping->getMethodName(CompleteTaskCommand::class);
    }
}
