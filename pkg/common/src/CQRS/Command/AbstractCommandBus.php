<?php

declare(strict_types=1);

namespace Warp\Common\CQRS\Command;

use Warp\CommandBus\CommandBus as MessageBus;

abstract class AbstractCommandBus implements CommandBusInterface
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
    final public function dispatch(CommandInterface $command): void
    {
        $this->bus->handle($command);
    }
}
