<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping;

use Warp\CommandBus\Mapping\ClassName\ClassNameMappingInterface;
use Warp\CommandBus\Mapping\Method\MethodNameMappingInterface;

final class CompositeMapping implements CommandToHandlerMappingInterface
{
    private ClassNameMappingInterface $classNameMapping;

    private MethodNameMappingInterface $methodNameMapping;

    public function __construct(
        ClassNameMappingInterface $classNameMapping,
        MethodNameMappingInterface $methodNameMapping
    ) {
        $this->classNameMapping = $classNameMapping;
        $this->methodNameMapping = $methodNameMapping;
    }

    public function getClassName(string $commandClass): string
    {
        return $this->classNameMapping->getClassName($commandClass);
    }

    public function getMethodName(string $commandClass): string
    {
        return $this->methodNameMapping->getMethodName($commandClass);
    }
}
