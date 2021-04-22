<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use Symfony\Component\Uid\Uuid;

class UuidValue extends AbstractStringValue
{
    /**
     * Creates new random uuid VO
     * @return static
     */
    public static function random(): self
    {
        return static::new(Uuid::v4());
    }

    protected static function validate($value): void
    {
        parent::validate($value);

        if (!Uuid::isValid((string)$value)) {
            throw new \InvalidArgumentException(\sprintf('Expected a value to be a valid uuid. Got: %s.', $value));
        }
    }
}
