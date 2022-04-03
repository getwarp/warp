<?php

declare(strict_types=1);

namespace Warp\CommandBus;

\class_alias(
    ExceptionInterface::class,
    __NAMESPACE__ . '\Exception'
);

if (false) {
    /**
     * @deprecated Use {@see \Warp\CommandBus\ExceptionInterface} instead.
     */
    interface Exception extends ExceptionInterface
    {
    }
}
