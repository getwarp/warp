<?php

declare(strict_types=1);

namespace spaceonfire\Container\Fixtures;

final class MethodsCallFixture
{
    public ?string $name = null;

    public ?string $color = null;

    public function __construct()
    {
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function withColor(string $color): self
    {
        $clone = clone $this;
        $clone->color = $color;
        return $clone;
    }
}
