<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

abstract class StringValue extends BaseValueObject
{
    protected function validate($value): bool
    {
        return is_scalar($value) || (is_object($value) && method_exists($value, '__toString'));
    }

    protected function cast($value): string
    {
        return (string)$value;
    }

    public function value(): string
    {
        return parent::value();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return (string)$this;
    }
}
