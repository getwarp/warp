<?php

declare(strict_types=1);

namespace spaceonfire\Container\Argument;

final class ArgumentValue
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * ArgumentValue constructor.
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Getter for `value` property
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
