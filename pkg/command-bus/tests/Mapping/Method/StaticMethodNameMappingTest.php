<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\Method;

use PHPUnit\Framework\TestCase;

class StaticMethodNameMappingTest extends TestCase
{
    public function testGetMethodName(): void
    {
        self::assertEquals('__invoke', (new StaticMethodNameMapping('__invoke'))->getMethodName('command'));
    }
}
