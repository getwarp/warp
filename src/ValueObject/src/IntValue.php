<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

abstract class IntValue extends BaseValueObject
{
    public function __construct(int $value)
    {
        parent::__construct($value);
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
