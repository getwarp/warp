<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

abstract class StringValue extends BaseValueObject
{
    public function __construct(string $value)
    {
        parent::__construct($value);
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
