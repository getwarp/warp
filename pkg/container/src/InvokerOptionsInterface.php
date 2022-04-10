<?php

declare(strict_types=1);

namespace Warp\Container;

interface InvokerOptionsInterface
{
    public function getArgumentAlias(string $argument): ?string;

    public function setArgumentAlias(string $argument, string $alias): self;

    public function getArgumentTag(string $argument): ?string;

    public function setArgumentTag(string $argument, string $tag): self;

    /**
     * @param string $argument
     * @param mixed $value
     * @return $this
     */
    public function addArgument(string $argument, $value): self;

    public function hasArgument(string $argument): bool;

    /**
     * @param string $argument
     * @return mixed|null
     */
    public function getArgument(string $argument);
}
