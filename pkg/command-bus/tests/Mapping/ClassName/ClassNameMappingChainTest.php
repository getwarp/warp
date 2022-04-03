<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping\ClassName;

use PHPUnit\Framework\TestCase;
use Warp\CommandBus\_Fixtures\Command\AddTaskCommand;
use Warp\CommandBus\_Fixtures\Handler\AddTaskCommandHandler;

class ClassNameMappingChainTest extends TestCase
{
    public function testGetClassName(): void
    {
        $mapping = new ClassNameMappingChain([
            new ReplacementClassNameMapping(
                'Warp\CommandBus\_Fixtures\Command',
                'Warp\CommandBus\_Fixtures\Handler'
            ),
            new SuffixClassNameMapping('Handler'),
        ]);

        self::assertEquals(AddTaskCommandHandler::class, $mapping->getClassName(AddTaskCommand::class));
    }
}
