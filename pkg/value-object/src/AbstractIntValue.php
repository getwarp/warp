<?php

declare(strict_types=1);

namespace Warp\ValueObject;

/**
 * @extends AbstractValueObject<int>
 */
abstract class AbstractIntValue extends AbstractValueObject
{
    public function jsonSerialize(): int
    {
        return $this->value();
    }

    public function gt(self $other): bool
    {
        return $this->value() > $other->value();
    }

    public function gte(self $other): bool
    {
        return $this->value() >= $other->value();
    }

    public function lt(self $other): bool
    {
        return $this->value() < $other->value();
    }

    public function lte(self $other): bool
    {
        return $this->value() <= $other->value();
    }

    protected static function validate($value): void
    {
        if (false === \filter_var($value, \FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException(
                \sprintf('%s accepts only integers. Got: %s.', static::class, \get_debug_type($value))
            );
        }
    }

    protected static function cast($value): int
    {
        return (int)$value;
    }
}
