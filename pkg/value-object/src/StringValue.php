<?php

declare(strict_types=1);

namespace Warp\ValueObject;

abstract class StringValue extends BaseValueObject
{
    /**
     * @inheritDoc
     * @return string
     */
    public function value(): string
    {
        return parent::value();
    }

    /**
     * @inheritDoc
     */
    protected function validate($value): bool
    {
        return is_scalar($value) || (is_object($value) && method_exists($value, '__toString'));
    }

    /**
     * @inheritDoc
     * @return string
     */
    protected function cast($value): string
    {
        return (string)$value;
    }
}
