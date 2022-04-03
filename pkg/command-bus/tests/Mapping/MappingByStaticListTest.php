<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping;

use PHPUnit\Framework\TestCase;
use Warp\CommandBus\_Fixtures\Command\AddTaskCommand;
use Warp\CommandBus\_Fixtures\Command\CompleteTaskCommand;
use Warp\CommandBus\_Fixtures\Handler\AddTaskCommandHandler;

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
