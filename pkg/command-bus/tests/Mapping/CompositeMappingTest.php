<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping;

use PHPUnit\Framework\TestCase;
use spaceonfire\CommandBus\Fixtures\Command\AddTaskCommand;
use spaceonfire\CommandBus\Fixtures\Handler\AddTaskCommandHandler;
use spaceonfire\CommandBus\Mapping\ClassName\ClassNameMappingChain;
use spaceonfire\CommandBus\Mapping\ClassName\ReplacementClassNameMapping;
use spaceonfire\CommandBus\Mapping\ClassName\SuffixClassNameMapping;
use spaceonfire\CommandBus\Mapping\Method\StaticMethodNameMapping;

class CompositeMappingTest extends TestCase
{
    public function testCompositeMapping(): void
    {
        $mapping = new CompositeMapping(
            new ClassNameMappingChain(
                new ReplacementClassNameMapping(
                    'spaceonfire\CommandBus\Fixtures\Command',
                    'spaceonfire\CommandBus\Fixtures\Handler'
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
