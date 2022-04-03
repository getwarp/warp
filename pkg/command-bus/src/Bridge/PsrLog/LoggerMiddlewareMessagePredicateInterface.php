<?php

declare(strict_types=1);

namespace Warp\CommandBus\Bridge\PsrLog;

interface LoggerMiddlewareMessagePredicateInterface
{
    /**
     * @param object $message
     * @return bool
     */
    public function __invoke(object $message): bool;
}
