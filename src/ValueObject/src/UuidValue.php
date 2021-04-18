<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

class UuidValue extends StringValue
{
    /**
     * @inheritDoc
     */
    protected function validate($value): bool
    {
        return parent::validate($value) && Uuid::isValid((string)$value);
    }

    /**
     * @inheritDoc
     */
    protected function throwExceptionForInvalidValue(?string $value): void
    {
        if ($value !== null) {
            throw new InvalidArgumentException(sprintf('Expected a value to be a valid uuid. Got "%s"', $value));
        }

        parent::throwExceptionForInvalidValue($value);
    } // @codeCoverageIgnore

    /**
     * Creates new random uuid VO
     * @return static
     */
    public static function random(): self
    {
        return new static(Uuid::uuid4()->toString());
    }
}
