<?php

declare(strict_types=1);

namespace Warp\Container\Argument;

use ReflectionFunctionAbstract;
use Warp\Container\ContainerAwareInterface;

interface ResolverInterface extends ContainerAwareInterface
{
    /**
     * Resolve function arguments.
     * @param ReflectionFunctionAbstract $reflection
     * @param array<string,mixed> $arguments
     * @return array<mixed>
     */
    public function resolveArguments(ReflectionFunctionAbstract $reflection, array $arguments = []): array;
}
