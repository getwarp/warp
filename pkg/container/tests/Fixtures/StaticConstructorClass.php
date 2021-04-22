<?php

declare(strict_types=1);

namespace spaceonfire\Container\Fixtures;

use spaceonfire\Common\Factory\StaticConstructorInterface;

final class StaticConstructorClass implements StaticConstructorInterface
{
    private ?MyClass $dependency;

    private function __construct(?MyClass $dependency)
    {
        $this->dependency = $dependency;
    }

    public static function new(MyClass $dependency): self
    {
        return new self($dependency);
    }

    public static function empty(): self
    {
        return new self(null);
    }

    public static function notConstructor(): void
    {
    }

    public function getDependency(): ?MyClass
    {
        return $this->dependency;
    }
}
