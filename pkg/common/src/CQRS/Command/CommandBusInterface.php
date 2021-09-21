<?php

declare(strict_types=1);

namespace spaceonfire\Common\CQRS\Command;

interface CommandBusInterface
{
    /**
     * Dispatches the command.
     * @param CommandInterface $command
     */
    public function dispatch(CommandInterface $command): void;
}
