<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping\ClassName;

final class SuffixClassNameMapping implements ClassNameMappingInterface
{
    private string $suffix;

    public function __construct(string $suffix)
    {
        $this->suffix = $suffix;
    }

    public function getClassName(string $commandClass): string
    {
        return $commandClass . $this->suffix;
    }
}
