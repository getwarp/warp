<?php

declare(strict_types=1);

namespace spaceonfire\Common\_Fixtures\CQRS\Query;

use Psr\Container\ContainerInterface;
use spaceonfire\CommandBus\CommandBus as MessageBus;
use spaceonfire\CommandBus\Mapping\ClassName\SuffixClassNameMapping;
use spaceonfire\CommandBus\Mapping\CompositeMapping;
use spaceonfire\CommandBus\Mapping\Method\StaticMethodNameMapping;
use spaceonfire\Common\CQRS\Query\AbstractQueryBus;

final class FixtureQueryBus extends AbstractQueryBus
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct(
            new MessageBus(
                new CompositeMapping(
                    new SuffixClassNameMapping('Handler'),
                    new StaticMethodNameMapping('__invoke')
                ),
                [],
                $container
            )
        );
    }
}
