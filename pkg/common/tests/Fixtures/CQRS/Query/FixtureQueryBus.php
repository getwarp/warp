<?php

declare(strict_types=1);

namespace spaceonfire\Common\Fixtures\CQRS\Query;

use spaceonfire\CommandBus\CommandBus as MessageBus;
use spaceonfire\CommandBus\Mapping\ClassName\SuffixClassNameMapping;
use spaceonfire\CommandBus\Mapping\CompositeMapping;
use spaceonfire\CommandBus\Mapping\Method\StaticMethodNameMapping;
use spaceonfire\Common\CQRS\Query\AbstractQueryBus;
use spaceonfire\Container\FactoryAggregateInterface;

final class FixtureQueryBus extends AbstractQueryBus
{
    public function __construct(FactoryAggregateInterface $factory)
    {
        parent::__construct(
            new MessageBus(
                new CompositeMapping(
                    new SuffixClassNameMapping('Handler'),
                    new StaticMethodNameMapping('__invoke')
                ),
                [],
                $factory
            )
        );
    }
}
