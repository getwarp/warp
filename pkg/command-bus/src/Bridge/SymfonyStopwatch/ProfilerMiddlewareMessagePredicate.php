<?php

declare(strict_types=1);

namespace Warp\CommandBus\Bridge\SymfonyStopwatch;

\class_alias(
    ProfilerMiddlewareMessagePredicateInterface::class,
    __NAMESPACE__ . '\ProfilerMiddlewareMessagePredicate'
);

if (false) {
    /**
     * @deprecated Use {@see \Warp\CommandBus\Bridge\SymfonyStopwatch\ProfilerMiddlewareMessagePredicateInterface} instead.
     */
    interface ProfilerMiddlewareMessagePredicate extends ProfilerMiddlewareMessagePredicateInterface
    {
    }
}
