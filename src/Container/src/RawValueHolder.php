<?php

declare(strict_types=1);

namespace spaceonfire\Container;

/**
 * Class RawValueHolder.
 * @final
 */
class RawValueHolder
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * RawValueHolder constructor.
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Getter for `value` property.
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
