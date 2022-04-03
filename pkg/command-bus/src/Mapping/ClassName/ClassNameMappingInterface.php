<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping\ClassName;

interface ClassNameMappingInterface
{
    /**
     * Returns handler class name for given command class name
     * @param string $commandClassName
     * @return string
     */
    public function getClassName(string $commandClassName): string;
}
