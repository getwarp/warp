<?php

declare(strict_types=1);

namespace spaceonfire\Common\CQRS\Query;

use spaceonfire\CommandBus\CommandBus as MessageBus;

abstract class AbstractQueryBus implements QueryBusInterface
{
    private MessageBus $bus;

    public function __construct(MessageBus $bus)
    {
        $this->bus = $bus;
    }

    public function ask(QueryInterface $query): ?ResponseInterface
    {
        return $this->bus->handle($query);
    }
}
