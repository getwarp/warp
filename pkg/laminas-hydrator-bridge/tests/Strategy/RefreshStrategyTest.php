<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\LaminasHydrator\Strategy;

use PHPUnit\Framework\TestCase;

class RefreshStrategyTest extends TestCase
{
    public function testDefault(): void
    {
        $strategy = new RefreshStrategy();
        self::assertNull($strategy->extract('some value'));
        self::assertNull($strategy->hydrate('some value'));
    }

    public function testCustomValue(): void
    {
        $strategy = new RefreshStrategy('foo');
        self::assertSame('foo', $strategy->extract('some value'));
        self::assertSame('foo', $strategy->hydrate('some value'));
    }
}
