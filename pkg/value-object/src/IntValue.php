<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

abstract class IntValue extends BaseValueObject
{
    /**
     * @inheritDoc
     * @return int
     */
    public function value(): int
    {
        return parent::value();
    }

    /**
     * Checks that current VO equals to provided one.
     * @param IntValue $other
     * @return bool
     * @deprecated replaced with equals() method on base value object class
     * @codeCoverageIgnore
     */
    public function equalsTo(self $other): bool
    {
        return $this->equals($other);
    }

    /**
     * Checks that current VO is bigger than provided one.
     * @param IntValue $other
     * @return bool
     */
    public function isBiggerThan(self $other): bool
    {
        return $this->value() > $other->value();
    }

    /**
     * @inheritDoc
     * @return int
     */
    public function jsonSerialize(): int
    {
        return $this->value();
    }

    /**
     * @inheritDoc
     */
    protected function validate($value): bool
    {
        return is_int($value) || (is_string($value) && preg_match('/^[+-]?\d+$/D', $value));
    }

    /**
     * @inheritDoc
     * @return int
     */
    protected function cast($value): int
    {
        return (int)$value;
    }
}
