<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\ClassName;

use PHPUnit\Framework\TestCase;
use spaceonfire\CommandBus\Fixtures\Command\AddTaskCommand;

class SuffixClassNameMappingTest extends TestCase
{
    public function testGetClassName(): void
    {
        $mapping = new SuffixClassNameMapping('Handler');
        self::assertEquals(AddTaskCommand::class . 'Handler', $mapping->getClassName(AddTaskCommand::class));
    }
}
