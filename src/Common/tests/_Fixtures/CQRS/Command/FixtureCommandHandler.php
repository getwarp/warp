<?php

declare(strict_types=1);

namespace spaceonfire\Common\_Fixtures\CQRS\Command;

use spaceonfire\Common\CQRS\Command\CommandInterface;
use SplObjectStorage;

final class FixtureCommandHandler
{
    private $handledCommands;

    public function __construct()
    {
        $this->handledCommands = new SplObjectStorage();
    }

    public function __invoke(FixtureCommand $command): void
    {
        $this->handledCommands->attach($command);
    }

    public function isHandled(CommandInterface $command): bool
    {
        return $this->handledCommands->contains($command);
    }
}
