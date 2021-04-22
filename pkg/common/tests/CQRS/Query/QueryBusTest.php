<?php

declare(strict_types=1);

namespace spaceonfire\Common\CQRS\Query;

use PHPUnit\Framework\TestCase;
use spaceonfire\Common\Fixtures\CQRS\Query\FixtureQuery;
use spaceonfire\Common\Fixtures\CQRS\Query\FixtureQueryBus;
use spaceonfire\Common\Fixtures\CQRS\Query\FixtureQueryHandler;
use spaceonfire\Common\Fixtures\CQRS\Query\FixtureQueryResponse;
use spaceonfire\Container\FactoryInterface;
use spaceonfire\Container\FactoryOptionsInterface;
use spaceonfire\Container\Fixtures\ArrayFactoryAggregate;

/**
 * @todo: replace ArrayFactoryAggregate
 */
class QueryBusTest extends TestCase
{
    public function testAsk(): void
    {
        $queryBus = new FixtureQueryBus(new ArrayFactoryAggregate([
            FixtureQueryHandler::class => new class implements FactoryInterface {
                public function make(?FactoryOptionsInterface $options = null)
                {
                    return new FixtureQueryHandler();
                }
            },
        ]));

        $queryResponse = $queryBus->ask(new FixtureQuery());

        self::assertInstanceOf(FixtureQueryResponse::class, $queryResponse);
    }

    public function testAskWithNoResponse(): void
    {
        $queryBus = new FixtureQueryBus(new ArrayFactoryAggregate([
            FixtureQueryHandler::class => new class implements FactoryInterface {
                public function make(?FactoryOptionsInterface $options = null)
                {
                    return new FixtureQueryHandler(true);
                }
            },
        ]));

        $queryResponse = $queryBus->ask(new FixtureQuery());

        self::assertNull($queryResponse);
    }
}
