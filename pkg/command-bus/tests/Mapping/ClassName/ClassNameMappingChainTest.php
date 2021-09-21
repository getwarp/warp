<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\ClassName;

use PHPUnit\Framework\TestCase;
use spaceonfire\CommandBus\_Fixtures\Command\AddTaskCommand;
use spaceonfire\CommandBus\_Fixtures\Handler\AddTaskCommandHandler;

class ClassNameMappingChainTest extends TestCase
{
    public function testGetClassName(): void
    {
        $mapping = new ClassNameMappingChain([
            new ReplacementClassNameMapping(
                'spaceonfire\CommandBus\_Fixtures\Command',
                'spaceonfire\CommandBus\_Fixtures\Handler'
            ),
            new SuffixClassNameMapping('Handler'),
        ]);

        self::assertEquals(AddTaskCommandHandler::class, $mapping->getClassName(AddTaskCommand::class));
    }
}
