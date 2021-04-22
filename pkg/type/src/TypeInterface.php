<?php

declare(strict_types=1);

namespace spaceonfire\Type;

interface TypeInterface extends \Stringable
{
    /**
     * Print type as a string
     * @return string
     */
    public function __toString(): string;

    /**
     * Check that type of given value satisfies constraints
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool;
}
