<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping\ClassName;

use PHPUnit\Framework\TestCase;
use Warp\CommandBus\_Fixtures\Command\AddTaskCommand;

class SuffixClassNameMappingTest extends TestCase
{
    public function testGetClassName(): void
    {
        $mapping = new SuffixClassNameMapping('Handler');
        self::assertEquals(AddTaskCommand::class . 'Handler', $mapping->getClassName(AddTaskCommand::class));
    }
}
