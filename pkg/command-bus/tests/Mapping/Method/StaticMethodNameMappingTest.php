<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping\Method;

use PHPUnit\Framework\TestCase;

class StaticMethodNameMappingTest extends TestCase
{
    public function testGetMethodName(): void
    {
        self::assertSame('__invoke', (new StaticMethodNameMapping('__invoke'))->getMethodName('command'));
    }
}
