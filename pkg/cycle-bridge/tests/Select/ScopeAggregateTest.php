<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Select;

use Cycle\ORM\Select\QueryConstrain;
use PHPUnit\Framework\TestCase;

class ScopeAggregateTest extends TestCase
{
    public function testAggregateMultipleScopes(): void
    {
        $scope1 = new QueryConstrain([]);
        $scope2 = new QueryConstrain([]);
        $scope3 = new QueryConstrain([]);

        $aggregate1 = new ScopeAggregate($scope1, $scope2);
        $aggregate2 = new ScopeAggregate($scope2, $scope3);
        $aggregateAll = new ScopeAggregate($aggregate1, $aggregate2);

        self::assertSame([$scope1, $scope2, $scope3], [...$aggregateAll]);
    }
}
