<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping;

\class_alias(
    CommandToHandlerMappingInterface::class,
    __NAMESPACE__ . '\CommandToHandlerMapping'
);

if (false) {
    /**
     * @deprecated Use {@see \Warp\CommandBus\Mapping\CommandToHandlerMappingInterface} instead.
     */
    interface CommandToHandlerMapping extends CommandToHandlerMappingInterface
    {
    }
}
