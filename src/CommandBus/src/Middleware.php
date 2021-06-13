<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus;

\class_alias(
    MiddlewareInterface::class,
    __NAMESPACE__ . '\Middleware'
);

if (false) {
    /**
     * @deprecated Use {@see \spaceonfire\CommandBus\MiddlewareInterface} instead.
     */
    interface Middleware extends MiddlewareInterface
    {
    }
}
