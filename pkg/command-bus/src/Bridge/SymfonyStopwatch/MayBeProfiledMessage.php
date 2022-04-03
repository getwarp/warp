<?php

declare(strict_types=1);

namespace Warp\CommandBus\Bridge\SymfonyStopwatch;

\class_alias(
    MayBeProfiledMessageInterface::class,
    __NAMESPACE__ . '\MayBeProfiledMessage'
);

if (false) {
    /**
     * @deprecated Use {@see \Warp\CommandBus\Bridge\SymfonyStopwatch\MayBeProfiledMessageInterface} instead.
     */
    interface MayBeProfiledMessage extends MayBeProfiledMessageInterface
    {
    }
}
