<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\ClassName;

use Webmozart\Assert\Assert;

class ClassNameMappingChain implements ClassNameMappingInterface
{
    /**
     * @var ClassNameMappingInterface[]
     */
    private $mappings;

    /**
     * ClassNameMappingChain constructor.
     * @param ClassNameMappingInterface[] $mappings
     */
    public function __construct(array $mappings)
    {
        Assert::notEmpty($mappings);
        Assert::allIsInstanceOf($mappings, ClassNameMappingInterface::class);
        $this->mappings = $mappings;
    }

    /**
     * @inheritDoc
     */
    public function getClassName(string $commandClassName): string
    {
        $result = $commandClassName;

        foreach ($this->mappings as $mapping) {
            $result = $mapping->getClassName($result);
        }

        return $result;
    }
}
