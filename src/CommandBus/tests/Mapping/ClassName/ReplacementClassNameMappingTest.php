<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\ClassName;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use spaceonfire\CommandBus\_Fixtures\Command\AddTaskCommand;

class ReplacementClassNameMappingTest extends TestCase
{
    /**
     * @param $search
     * @param $replace
     * @dataProvider constructArgumentsProvider
     */
    public function testConstruct($search, $replace): void
    {
        new ReplacementClassNameMapping($search, $replace);
        self::assertTrue(true);
    }

    public function constructArgumentsProvider(): array
    {
        return [
            ['search', 'replace'],
            [['search1', 'search2'], 'replace'],
            [['search' => 'replace'], null],
        ];
    }

    /**
     * @param $search
     * @param $replace
     * @dataProvider constructExceptionArgumentsProvider
     */
    public function testConstructException($search, $replace): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplacementClassNameMapping($search, $replace);
    }

    public function constructExceptionArgumentsProvider(): array
    {
        return [
            [0, 1],
            ['search', null],
            [['search1', 'search2'], null],
        ];
    }

    public function testGetClassName(): void
    {
        $mapping = new ReplacementClassNameMapping('spaceonfire\CommandBus\_Fixtures\Command', 'spaceonfire\CommandBus\_Fixtures\Handler');

        self::assertEquals(
            'spaceonfire\CommandBus\_Fixtures\Handler\AddTaskCommand',
            $mapping->getClassName(AddTaskCommand::class)
        );
    }
}
