<?php

/**
 * @phpcs:disable PSR12.Classes.ClosingBrace.StatementAfter
 */

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

abstract class UuidValue extends StringValue
{
    protected function validate($value): bool
    {
        return parent::validate($value) && Uuid::isValid((string)$value);
    }

    public static function random(): self
    {
        return new static(Uuid::uuid4()->toString());
    }

    protected function throwExceptionForInvalidValue(?string $value): void
    {
        if ($value !== null) {
            throw new InvalidArgumentException(
                sprintf('Expected a value to be a valid uuid. Got "%s"', $value)
            );
        }

        parent::throwExceptionForInvalidValue($value);
    } // @codeCoverageIgnore
}
