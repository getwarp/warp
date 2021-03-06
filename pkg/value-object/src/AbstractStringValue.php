<?php

declare(strict_types=1);

namespace Warp\ValueObject;

/**
 * @extends AbstractValueObject<string>
 */
abstract class AbstractStringValue extends AbstractValueObject
{
    public function jsonSerialize(): string
    {
        return (string)$this->value;
    }

    protected static function validate($value): void
    {
        if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
            throw new \InvalidArgumentException(\sprintf(
                '%s accepts only scalars or instances of \Stringable. Got: %s.',
                static::class,
                \get_debug_type($value)
            ));
        }
    }

    protected static function cast($value): string
    {
        return (string)$value;
    }
}
