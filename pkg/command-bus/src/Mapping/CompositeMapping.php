<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping;

use Warp\CommandBus\Mapping\ClassName\ClassNameMappingInterface;
use Warp\CommandBus\Mapping\Method\MethodNameMappingInterface;

final class CompositeMapping implements CommandToHandlerMappingInterface
{
    /**
     * @var ClassNameMappingInterface
     */
    private $classNameMapping;

    /**
     * @var MethodNameMappingInterface
     */
    private $methodNameMapping;

    /**
     * CompositeMapping constructor.
     * @param ClassNameMappingInterface $classNameMapping
     * @param MethodNameMappingInterface $methodNameMapping
     */
    public function __construct(
        ClassNameMappingInterface $classNameMapping,
        MethodNameMappingInterface $methodNameMapping
    ) {
        $this->classNameMapping = $classNameMapping;
        $this->methodNameMapping = $methodNameMapping;
    }

    /**
     * @inheritDoc
     */
    public function getClassName(string $commandClassName): string
    {
        return $this->classNameMapping->getClassName($commandClassName);
    }

    /**
     * @inheritDoc
     */
    public function getMethodName(string $commandClassName): string
    {
        return $this->methodNameMapping->getMethodName($commandClassName);
    }
}
