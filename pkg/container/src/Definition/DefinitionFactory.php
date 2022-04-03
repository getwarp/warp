<?php

declare(strict_types=1);

namespace Warp\Container\Definition;

final class DefinitionFactory implements DefinitionFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function make(string $abstract, $concrete = null, bool $shared = false): DefinitionInterface
    {
        return new Definition($abstract, $concrete, $shared);
    }
}
