<?php

declare(strict_types=1);

namespace Warp\CommandBus;

/**
 * Middleware allows you to extend functionality of CommandBus.
 * They receive each command that's given to the CommandBus and can take any action they choose.
 * Middleware can continue the Command processing by passing the command they receive to the
 * `$next` callable, which is essentially the "next" Middleware in the chain.
 *
 * Depending on where they invoke the $next callable, Middleware can execute
 * their custom logic before or after the Command is handled. They can also
 * modify, log, or replace the command they receive. The sky's the limit.
 */
interface MiddlewareInterface
{
    /**
     * @param object $command
     * @param callable(object):mixed $next
     * @return mixed
     */
    public function execute(object $command, callable $next);
}
