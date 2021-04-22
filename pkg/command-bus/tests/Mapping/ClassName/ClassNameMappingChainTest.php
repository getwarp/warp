<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\ClassName;

use PHPUnit\Framework\TestCase;
use spaceonfire\CommandBus\Fixtures\Command\AddTaskCommand;
use spaceonfire\CommandBus\Fixtures\Handler\AddTaskCommandHandler;

class ClassNameMappingChainTest extends TestCase
{
    public function testGetClassName(): void
    {
        $mapping = new ClassNameMappingChain(
            new ReplacementClassNameMapping(
                'spaceonfire\CommandBus\Fixtures\Command',
                'spaceonfire\CommandBus\Fixtures\Handler'
            ),
            new SuffixClassNameMapping('Handler'),
        );

        self::assertSame(AddTaskCommandHandler::class, $mapping->getClassName(AddTaskCommand::class));
    }
}
