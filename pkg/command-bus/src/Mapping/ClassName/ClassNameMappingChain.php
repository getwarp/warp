<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\ClassName;

final class ClassNameMappingChain implements ClassNameMappingInterface
{
    /**
     * @var ClassNameMappingInterface[]
     */
    private array $mappings;

    public function __construct(ClassNameMappingInterface ...$mappings)
    {
        $this->mappings = $mappings;
    }

    public function getClassName(string $commandClass): string
    {
        return \array_reduce(
            $this->mappings,
            static fn (string $carry, ClassNameMappingInterface $mapping) => $mapping->getClassName($carry),
            $commandClass,
        );
    }
}
