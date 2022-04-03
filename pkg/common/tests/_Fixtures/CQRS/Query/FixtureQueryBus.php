<?php

declare(strict_types=1);

namespace Warp\Common\_Fixtures\CQRS\Query;

use Psr\Container\ContainerInterface;
use Warp\CommandBus\CommandBus as MessageBus;
use Warp\CommandBus\Mapping\ClassName\SuffixClassNameMapping;
use Warp\CommandBus\Mapping\CompositeMapping;
use Warp\CommandBus\Mapping\Method\StaticMethodNameMapping;
use Warp\Common\CQRS\Query\AbstractQueryBus;

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
