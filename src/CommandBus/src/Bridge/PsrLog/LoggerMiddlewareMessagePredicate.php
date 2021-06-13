<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Bridge\PsrLog;

\class_alias(
    LoggerMiddlewareMessagePredicateInterface::class,
    __NAMESPACE__ . '\LoggerMiddlewareMessagePredicate'
);

if (false) {
    /**
     * @deprecated Use {@see \spaceonfire\CommandBus\Bridge\PsrLog\LoggerMiddlewareMessagePredicateInterface} instead.
     */
    interface LoggerMiddlewareMessagePredicate extends LoggerMiddlewareMessagePredicateInterface
    {
    }
}
