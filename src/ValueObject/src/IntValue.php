<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

abstract class IntValue extends BaseValueObject
{
    protected function validate($value): bool
    {
        return is_int($value) || (is_string($value) && preg_match('/^[+-]?\d+$/D', $value));
    }

    protected function cast($value)
    {
        return (int)$value;
    }

    public function value(): int
    {
        return parent::value();
    }

    public function equalsTo(IntValue $other): bool
    {
        return $this->value() === $other->value();
    }

    public function isBiggerThan(IntValue $other): bool
    {
        return $this->value() > $other->value();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->value();
    }
}
