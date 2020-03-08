<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use JsonSerializable;

abstract class BaseValueObject implements JsonSerializable
{
    /**
     * @var mixed
     */
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function value()
    {
        return $this->value;
    }

    /**
     * Cast value object to string
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value();
    }
}
