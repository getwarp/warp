<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping\ClassName;

use PHPUnit\Framework\TestCase;
use Warp\CommandBus\Fixtures\Command\AddTaskCommand;
use Warp\CommandBus\Fixtures\Handler\AddTaskCommandHandler;

class ClassNameMappingChainTest extends TestCase
{
    public function testGetClassName(): void
    {
        $mapping = new ClassNameMappingChain(
            new ReplacementClassNameMapping(
                'Warp\CommandBus\Fixtures\Command',
                'Warp\CommandBus\Fixtures\Handler'
            ),
            new SuffixClassNameMapping('Handler'),
        );

        self::assertSame(AddTaskCommandHandler::class, $mapping->getClassName(AddTaskCommand::class));
    }
}
