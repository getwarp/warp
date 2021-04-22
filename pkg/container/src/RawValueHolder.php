<?php

declare(strict_types=1);

namespace spaceonfire\Container;

/**
 * @template T
 */
final class RawValueHolder
{
    /**
     * @var T
     */
    private $value;

    /**
     * @param T $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return T
     */
    public function getValue()
    {
        return $this->value;
    }
}
