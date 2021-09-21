<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Bridge\CycleOrm\Mapper\Hydrator\Strategy;

use Cycle\ORM\Promise\Collection\CollectionPromise;
use Cycle\ORM\Promise\PromiseMany;
use Cycle\ORM\Promise\Reference;
use Cycle\ORM\Relation\Pivoted\PivotedCollection as CyclePivotedCollection;
use spaceonfire\Collection\Collection;
use spaceonfire\DataSource\Bridge\CycleOrm\AbstractCycleOrmTest;
use spaceonfire\DataSource\Bridge\CycleOrm\Collection\PivotedCollection;
use SplObjectStorage;

class CollectionExtractorStrategyTest extends AbstractCycleOrmTest
{
    /**
     * @var CollectionExtractorStrategy
     */
    private $strategy;

    protected function setUp(): void
    {
        parent::setUp();

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

    public function testExtractPromise(): void
    {
        $reference = new Reference('post', ['authorId' => 1]);
        self::assertEquals($reference, $this->strategy->extract($reference));
    }

    public function testExtractPromiseCollection(): void
    {
        $reference = new CollectionPromise(new PromiseMany(self::getOrm(), 'post', ['authorId' => 1]));
        self::assertEquals($reference->getPromise(), $this->strategy->extract($reference));
    }

    public function testExtractPivotedCollection(): void
    {
        $spaceonfireCollection = new PivotedCollection([1, 2, 3]);
        $spaceonfireCollection->setPivotContext(new SplObjectStorage());
        $doctrineCollection = $this->strategy->extract($spaceonfireCollection);
        self::assertInstanceOf(CyclePivotedCollection::class, $doctrineCollection);
        self::assertEquals($spaceonfireCollection->all(), iterator_to_array($doctrineCollection));
        self::assertEquals($spaceonfireCollection->getPivotContext(), $doctrineCollection->getPivotContext());
    }
}
