<?php

declare(strict_types=1);

namespace spaceonfire\Container\Argument;

use ReflectionFunctionAbstract;
use spaceonfire\Container\ContainerAwareInterface;

interface ResolverInterface extends ContainerAwareInterface
{
    /**
     * Resolves function arguments
     * @param ReflectionFunctionAbstract $reflection
     * @param array<string,mixed> $arguments
     * @return array<mixed>
     */
    public function resolveArguments(ReflectionFunctionAbstract $reflection, array $arguments = []): array;
}
