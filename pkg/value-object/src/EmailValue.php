<?php

declare(strict_types=1);

namespace Warp\ValueObject;

class EmailValue extends AbstractStringValue
{
    protected static function validate($value): void
    {
        parent::validate($value);

        if (false === \filter_var($value, \FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                \sprintf('Expected a value to be a valid e-mail address. Got: %s.', $value)
            );
        }
    }
}
