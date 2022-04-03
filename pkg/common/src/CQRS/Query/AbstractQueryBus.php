<?php

declare(strict_types=1);

namespace Warp\Common\CQRS\Query;

use Warp\CommandBus\CommandBus as MessageBus;

abstract class AbstractQueryBus implements QueryBusInterface
{
    /**
     * @var MessageBus
     */
    private $bus;

    public function __construct(MessageBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @inheritDoc
     */
    public function ask(QueryInterface $query): ?ResponseInterface
    {
        return $this->bus->handle($query);
    }
}
