<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Bridge\SymfonyStopwatch;

\class_alias(
    MayBeProfiledMessageInterface::class,
    __NAMESPACE__ . '\MayBeProfiledMessage'
);

if (false) {
    /**
     * @deprecated Use {@see \spaceonfire\CommandBus\Bridge\SymfonyStopwatch\MayBeProfiledMessageInterface} instead.
     */
    interface MayBeProfiledMessage extends MayBeProfiledMessageInterface
    {
    }
}
