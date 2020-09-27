<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Bridge\SymfonyStopwatch;

interface ProfilerMiddlewareMessagePredicate
{
    /**
     * @param object $message
     * @return bool
     */
    public function __invoke(object $message): bool;
}
