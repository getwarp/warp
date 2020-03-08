<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

abstract class UuidValue extends StringValue
{
    public function __construct(string $value)
    {
        $this->ensureIsValidUuid($value);
        parent::__construct($value);
    }

    public static function random(): self
    {
        return new static(Uuid::uuid4()->toString());
    }

    private function ensureIsValidUuid($id): void
    {
        if (!Uuid::isValid($id)) {
            throw new InvalidArgumentException(
                sprintf('<%s> does not allow the value <%s>.', static::class, is_scalar($id) ? $id : gettype($id))
            );
        }
    }
}
