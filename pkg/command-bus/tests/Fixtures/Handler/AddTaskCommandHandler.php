<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Fixtures\Handler;

use spaceonfire\CommandBus\Fixtures\Command\AddTaskCommand;

class AddTaskCommandHandler
{
    /**
     * @param AddTaskCommand $command
     * @return string
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(AddTaskCommand $command): string
    {
        return 'foobar';
    }
}
