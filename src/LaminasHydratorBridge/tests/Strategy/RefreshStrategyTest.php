<?php

declare(strict_types=1);

namespace spaceonfire\LaminasHydratorBridge\Strategy;

use PHPUnit\Framework\TestCase;

class RefreshStrategyTest extends TestCase
{
    public function testExtract(): void
    {
        $strategy = new RefreshStrategy();
        self::assertNull($strategy->extract('some value', null));
    }

    public function testHydrate(): void
    {
        $strategy = new RefreshStrategy();
        self::assertNull($strategy->hydrate('some value', null));
    }
}
