<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\Method;

interface MethodNameMappingInterface
{
    /**
     * Returns handler method name for given command class name
     * @param class-string $commandClass
     * @return string
     */
    public function getMethodName(string $commandClass): string;
}
