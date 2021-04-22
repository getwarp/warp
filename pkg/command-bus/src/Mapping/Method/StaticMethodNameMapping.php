<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\Method;

final class StaticMethodNameMapping implements MethodNameMappingInterface
{
    private string $methodName;

    public function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }

    public function getMethodName(string $commandClass): string
    {
        return $this->methodName;
    }
}
