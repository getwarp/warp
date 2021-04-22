<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Fixtures\Handler;

/**
 * Sample handler that has all commands specified as individual methods, rather
 * than using magic methods like __call or __invoke.
 */
class HandleMethodHandler
{
    /**
     * @param object $command
     * @return mixed|void
     */
    public function handle(object $command)
    {
    }
}
