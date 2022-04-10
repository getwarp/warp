<?php

declare(strict_types=1);

namespace Warp\Common\Fixtures\CQRS\Query;

use Warp\CommandBus\CommandBus as MessageBus;
use Warp\CommandBus\Mapping\ClassName\SuffixClassNameMapping;
use Warp\CommandBus\Mapping\CompositeMapping;
use Warp\CommandBus\Mapping\Method\StaticMethodNameMapping;
use Warp\Common\CQRS\Query\AbstractQueryBus;
use Warp\Container\FactoryAggregateInterface;

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
