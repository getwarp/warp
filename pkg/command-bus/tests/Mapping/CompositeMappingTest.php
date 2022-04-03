<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping;

use PHPUnit\Framework\TestCase;
use Warp\CommandBus\_Fixtures\Command\AddTaskCommand;
use Warp\CommandBus\_Fixtures\Handler\AddTaskCommandHandler;
use Warp\CommandBus\Mapping\ClassName\ClassNameMappingChain;
use Warp\CommandBus\Mapping\ClassName\ReplacementClassNameMapping;
use Warp\CommandBus\Mapping\ClassName\SuffixClassNameMapping;
use Warp\CommandBus\Mapping\Method\StaticMethodNameMapping;

class CompositeMappingTest extends TestCase
{
    private $mapping;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapping = new CompositeMapping(
            new ClassNameMappingChain([
                new ReplacementClassNameMapping(
                    'Warp\CommandBus\_Fixtures\Command',
                    'Warp\CommandBus\_Fixtures\Handler'
                ),
                new SuffixClassNameMapping('Handler'),
            ]),
            new StaticMethodNameMapping('handle')
        );
    }

    public function testGetClassName(): void
    {
        static::assertEquals(
            AddTaskCommandHandler::class,
            $this->mapping->getClassName(AddTaskCommand::class)
        );
    }

    public function testGetMethodName(): void
    {
        static::assertEquals(
            'handle',
            $this->mapping->getMethodName(AddTaskCommand::class)
        );
    }
}
