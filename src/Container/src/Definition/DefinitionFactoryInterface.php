<?php

declare(strict_types=1);

namespace spaceonfire\Container\Definition;

interface DefinitionFactoryInterface
{
    /**
     * Make definition object.
     * @param string $abstract
     * @param mixed $concrete
     * @param bool $shared
     * @return DefinitionInterface
     */
    public function make(string $abstract, $concrete = null, bool $shared = false): DefinitionInterface;
}
