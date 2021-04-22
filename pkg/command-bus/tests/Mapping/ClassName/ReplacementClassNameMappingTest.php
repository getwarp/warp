<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\ClassName;

use PHPUnit\Framework\TestCase;
use spaceonfire\CommandBus\Fixtures\Command\AddTaskCommand;

class ReplacementClassNameMappingTest extends TestCase
{
    public function testSimpleSearchReplace(): void
    {
        $mapping = new ReplacementClassNameMapping('Foo', 'Bar');
        self::assertSame('BarClass', $mapping->getClassName('FooClass'));
    }

    public function testSearchReplaceMultiple(): void
    {
        $mapping = new ReplacementClassNameMapping(['Foo', 42], ['Bar', 24]);
        self::assertSame('BarClass24', $mapping->getClassName('FooClass42'));
    }

    public function testSearchMultipleReplaceWithOne(): void
    {
        $mapping = new ReplacementClassNameMapping(['Foo', 'Bar'], 'Baz');
        self::assertSame('BazBaz', $mapping->getClassName('FooBar'));
    }

    public function testSearchReplaceMap(): void
    {
        $mapping = new ReplacementClassNameMapping([
            'Foo' => 'Bar',
        ]);
        self::assertSame('BarClass', $mapping->getClassName('FooClass'));
    }

    public function testRealWorldExample(): void
    {
        // Change namespace
        $mapping = new ReplacementClassNameMapping(
            'spaceonfire\CommandBus\Fixtures\Command',
            'spaceonfire\CommandBus\Fixtures\Handler',
        );

        self::assertSame(
            'spaceonfire\CommandBus\Fixtures\Handler\AddTaskCommand',
            $mapping->getClassName(AddTaskCommand::class)
        );
    }
}
