<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\ClassName;

interface ClassNameMappingInterface
{
    /**
     * Returns handler class name for given command class name
     * @param class-string $commandClass
     * @return class-string
     */
    public function getClassName(string $commandClass): string;
}
