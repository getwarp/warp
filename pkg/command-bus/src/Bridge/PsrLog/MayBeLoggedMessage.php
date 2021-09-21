<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Bridge\PsrLog;

\class_alias(
    MayBeLoggedMessageInterface::class,
    __NAMESPACE__ . '\MayBeLoggedMessage'
);

if (false) {
    /**
     * @deprecated Use {@see \spaceonfire\CommandBus\Bridge\PsrLog\MayBeLoggedMessageInterface} instead.
     */
    interface MayBeLoggedMessage extends MayBeLoggedMessageInterface
    {
    }
}
