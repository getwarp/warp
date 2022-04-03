<?php

declare(strict_types=1);

namespace Warp\CommandBus\_Fixtures\Handler;

use Warp\CommandBus\_Fixtures\Command\AddTaskCommand;

class AddTaskCommandHandler
{
    /**
     * @param AddTaskCommand $command
     * @return mixed|void
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(AddTaskCommand $command)
    {
        return 'foobar';
    }
}
