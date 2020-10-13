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
}
