<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Mapper\Hydrator\Strategy;

use PHPUnit\Framework\TestCase;
use spaceonfire\Collection\Collection;

class CollectionExtractorStrategyTest extends TestCase
{
    /**
     * @var CollectionExtractorStrategy
     */
    private $strategy;

    protected function setUp(): void
    {
        $this->strategy = new CollectionExtractorStrategy();
    }

    public function testHydrate(): void
    {
        self::assertEquals([], $this->strategy->hydrate([], null));
    }

    public function testExtract(): void
    {
        $spaceonfireCollection = new Collection([1, 2, 3]);
        $doctrineCollection = $this->strategy->extract($spaceonfireCollection);
        self::assertEquals($spaceonfireCollection->all(), iterator_to_array($doctrineCollection));
    }
}
