<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

class IpValue extends AbstractStringValue
{
    protected static function validate($value): void
    {
        parent::validate($value);

        if (false === \filter_var($value, \FILTER_VALIDATE_IP)) {
            throw new \InvalidArgumentException(
                \sprintf('%s expected a value to be an IP. Got: %s.', static::class, $value)
            );
        }
    }
}
