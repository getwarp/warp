<?php

declare(strict_types=1);

namespace spaceonfire\Common\CQRS\Query;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use spaceonfire\Common\_Fixtures\CQRS\Query\FixtureQuery;
use spaceonfire\Common\_Fixtures\CQRS\Query\FixtureQueryBus;
use spaceonfire\Common\_Fixtures\CQRS\Query\FixtureQueryHandler;
use spaceonfire\Common\_Fixtures\CQRS\Query\FixtureQueryResponse;

class QueryBusTest extends TestCase
{
    public function testAsk(): void
    {
        $queryHandler = new FixtureQueryHandler();

        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->has(FixtureQueryHandler::class)->willReturn(true);
        $containerProphecy->get(FixtureQueryHandler::class)->willReturn($queryHandler);

        $queryBus = new FixtureQueryBus($containerProphecy->reveal());

        $queryResponse = $queryBus->ask(new FixtureQuery());

        self::assertInstanceOf(FixtureQueryResponse::class, $queryResponse);
    }

    public function testAskWithNoResponse(): void
    {
        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->has(FixtureQueryHandler::class)->shouldBeCalled()->willReturn(true);
        $containerProphecy->get(FixtureQueryHandler::class)->shouldBeCalled()->willReturn(static function (): void {
        });

        $queryBus = new FixtureQueryBus($containerProphecy->reveal());

        $queryResponse = $queryBus->ask(new FixtureQuery());

        self::assertNull($queryResponse);
    }
}
