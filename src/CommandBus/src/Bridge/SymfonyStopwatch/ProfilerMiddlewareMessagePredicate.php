<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Bridge\SymfonyStopwatch;

\class_alias(
    ProfilerMiddlewareMessagePredicateInterface::class,
    __NAMESPACE__ . '\ProfilerMiddlewareMessagePredicate'
);

if (false) {
    /**
     * @deprecated Use {@see \spaceonfire\CommandBus\Bridge\SymfonyStopwatch\ProfilerMiddlewareMessagePredicateInterface} instead.
     */
    interface ProfilerMiddlewareMessagePredicate extends ProfilerMiddlewareMessagePredicateInterface
    {
    }
}
