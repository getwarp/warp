<?php

declare(strict_types=1);

namespace Warp\LaminasHydratorBridge\NamingStrategy;

use PHPUnit\Framework\TestCase;

class AliasNamingStrategyTest extends TestCase
{
    public function testHydrate(): void
    {
        $strategy = new AliasNamingStrategy([
            'verbosityLevelMap' => ['verbosity_level_map', 'verbosity-level-map'],
        ]);

        self::assertSame('verbosityLevelMap', $strategy->hydrate('verbosity_level_map'));
        self::assertSame('verbosityLevelMap', $strategy->hydrate('verbosity-level-map'));
        self::assertSame('verbosityLevelMap', $strategy->hydrate('verbosityLevelMap'));
    }

    public function testExtract(): void
    {
        $strategy = new AliasNamingStrategy([
            'verbosityLevelMap' => ['verbosity_level_map', 'verbosity-level-map'],
        ]);

        self::assertSame('verbosityLevelMap', $strategy->extract('verbosityLevelMap'));
    }

    public function testAddAlias(): void
    {
        $strategy = new AliasNamingStrategy();
        $strategy->addAlias('verbosityLevelMap', 'verbosity_level_map');
        $strategy->addAlias('verbosityLevelMap', 'verbosity_level_map');
        $this->expectNotice();
        $strategy->addAlias('overriddenName', 'verbosity_level_map');
        self::assertSame('overriddenName', $strategy->hydrate('verbosity_level_map'));
    }
}
