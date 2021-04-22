<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper;

use Cycle\ORM\Command\CommandInterface;

abstract class AbstractCommand implements CommandInterface
{
    private bool $executed = false;

    public function isExecuted(): bool
    {
        return $this->executed;
    }

    public function execute(): void
    {
        $this->executed = true;
    }

    public function complete(): void
    {
    }

    public function rollBack(): void
    {
    }
}
