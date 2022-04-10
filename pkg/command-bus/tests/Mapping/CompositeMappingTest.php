<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping;

use PHPUnit\Framework\TestCase;
use Warp\CommandBus\Fixtures\Command\AddTaskCommand;
use Warp\CommandBus\Fixtures\Handler\AddTaskCommandHandler;
use Warp\CommandBus\Mapping\ClassName\ClassNameMappingChain;
use Warp\CommandBus\Mapping\ClassName\ReplacementClassNameMapping;
use Warp\CommandBus\Mapping\ClassName\SuffixClassNameMapping;
use Warp\CommandBus\Mapping\Method\StaticMethodNameMapping;

class CompositeMappingTest extends TestCase
{
    public function testCompositeMapping(): void
    {
        $mapping = new CompositeMapping(
            new ClassNameMappingChain(
                new ReplacementClassNameMapping(
                    'Warp\CommandBus\Fixtures\Command',
                    'Warp\CommandBus\Fixtures\Handler'
                ),
                new SuffixClassNameMapping('Handler'),
            ),
            new StaticMethodNameMapping('handle')
        );

        self::assertSame(
            AddTaskCommandHandler::class,
            $mapping->getClassName(AddTaskCommand::class)
        );

        self::assertSame(
            'handle',
            $mapping->getMethodName(AddTaskCommand::class)
        );
    }
}
