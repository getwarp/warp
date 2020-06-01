<?php

declare(strict_types=1);

namespace spaceonfire\Type;

interface Type
{
    /**
     * Check that type of given value satisfies constraints
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool;

    /**
     * Print type as a string
     * @return string
     */
    public function __toString(): string;

    /**
     * Check that given string can be used to create a new instance
     * @param string $type
     * @return bool
     */
    public static function supports(string $type): bool;

    /**
     * Create new instance from a string
     * @param string $type
     * @return static|self
     */
    public static function create(string $type): self;
}
