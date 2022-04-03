<?php

declare(strict_types=1);

namespace Warp\CommandBus;

\class_alias(
    MiddlewareInterface::class,
    __NAMESPACE__ . '\Middleware'
);

if (false) {
    /**
     * @deprecated Use {@see \Warp\CommandBus\MiddlewareInterface} instead.
     */
    interface Middleware extends MiddlewareInterface
    {
    }
}
