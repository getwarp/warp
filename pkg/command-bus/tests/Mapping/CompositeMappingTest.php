<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping;

use PHPUnit\Framework\TestCase;
use spaceonfire\CommandBus\_Fixtures\Command\AddTaskCommand;
use spaceonfire\CommandBus\_Fixtures\Handler\AddTaskCommandHandler;
use spaceonfire\CommandBus\Mapping\ClassName\ClassNameMappingChain;
use spaceonfire\CommandBus\Mapping\ClassName\ReplacementClassNameMapping;
use spaceonfire\CommandBus\Mapping\ClassName\SuffixClassNameMapping;
use spaceonfire\CommandBus\Mapping\Method\StaticMethodNameMapping;

class CompositeMappingTest extends TestCase
{
    private $mapping;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapping = new CompositeMapping(
            new ClassNameMappingChain([
                new ReplacementClassNameMapping(
                    'spaceonfire\CommandBus\_Fixtures\Command',
                    'spaceonfire\CommandBus\_Fixtures\Handler'
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
