<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use spaceonfire\Exception\PackageMissingException;
use Symfony\Component\Uid\Uuid;

class UuidValue extends AbstractStringValue
{
    /**
     * Creates new random uuid VO
     * @return static
     */
    public static function random(): self
    {
        self::checkDependency();

        return static::new(Uuid::v4());
    }

    protected static function validate($value): void
    {
        self::checkDependency();

        parent::validate($value);

        if (!Uuid::isValid((string)$value)) {
            throw new \InvalidArgumentException(\sprintf('Expected a value to be a valid uuid. Got: %s.', $value));
        }
    }

    private static function checkDependency(): void
    {
        if (!\class_exists(Uuid::class)) {
            throw PackageMissingException::new('symfony/uid', '^5.3');
        }
    }
}
