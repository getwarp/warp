<?php

declare(strict_types=1);

namespace Warp\CommandBus\Bridge\PsrLog;

\class_alias(
    MayBeLoggedMessageInterface::class,
    __NAMESPACE__ . '\MayBeLoggedMessage'
);

if (false) {
    /**
     * @deprecated Use {@see \Warp\CommandBus\Bridge\PsrLog\MayBeLoggedMessageInterface} instead.
     */
    interface MayBeLoggedMessage extends MayBeLoggedMessageInterface
    {
    }
}
