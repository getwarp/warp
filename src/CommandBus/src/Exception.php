<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus;

\class_alias(
    ExceptionInterface::class,
    __NAMESPACE__ . '\Exception'
);

if (false) {
    /**
     * @deprecated Use {@see \spaceonfire\CommandBus\ExceptionInterface} instead.
     */
    interface Exception extends ExceptionInterface
    {
    }
}
