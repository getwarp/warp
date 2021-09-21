<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping;

\class_alias(
    CommandToHandlerMappingInterface::class,
    __NAMESPACE__ . '\CommandToHandlerMapping'
);

if (false) {
    /**
     * @deprecated Use {@see \spaceonfire\CommandBus\Mapping\CommandToHandlerMappingInterface} instead.
     */
    interface CommandToHandlerMapping extends CommandToHandlerMappingInterface
    {
    }
}
