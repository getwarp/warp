<?php

declare(strict_types=1);

namespace Warp\CommandBus\Bridge\PsrLog;

\class_alias(
    LoggerMiddlewareMessagePredicateInterface::class,
    __NAMESPACE__ . '\LoggerMiddlewareMessagePredicate'
);

if (false) {
    /**
     * @deprecated Use {@see \Warp\CommandBus\Bridge\PsrLog\LoggerMiddlewareMessagePredicateInterface} instead.
     */
    interface LoggerMiddlewareMessagePredicate extends LoggerMiddlewareMessagePredicateInterface
    {
    }
}
