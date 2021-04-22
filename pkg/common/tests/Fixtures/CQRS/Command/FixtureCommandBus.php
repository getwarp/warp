<?php

declare(strict_types=1);

namespace spaceonfire\Common\Fixtures\CQRS\Command;

use spaceonfire\CommandBus\CommandBus as MessageBus;
use spaceonfire\CommandBus\Mapping\ClassName\SuffixClassNameMapping;
use spaceonfire\CommandBus\Mapping\CompositeMapping;
use spaceonfire\CommandBus\Mapping\Method\StaticMethodNameMapping;
use spaceonfire\Common\CQRS\Command\AbstractCommandBus;
use spaceonfire\Container\FactoryAggregateInterface;

final class FixtureCommandBus extends AbstractCommandBus
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
