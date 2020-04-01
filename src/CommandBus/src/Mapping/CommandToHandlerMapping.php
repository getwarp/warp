<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping;

interface CommandToHandlerMapping
{
    /**
     * Returns handler class name for given command class name
     * @param string $commandClassName
     * @return string
     */
    public function getClassName(string $commandClassName): string;

    /**
     * Returns handler method name for given command class name
     * @param string $commandClassName
     * @return string
     */
    public function getMethodName(string $commandClassName): string;
}
