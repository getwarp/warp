<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use InvalidArgumentException;
use JsonSerializable;
use spaceonfire\ValueObject\Helpers\StringHelper;

abstract class BaseValueObject implements JsonSerializable
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * VO constructor
     * @param mixed $value
     */
    final public function __construct($value)
    {
        if (!$this->validate($value)) {
            $this->throwExceptionForInvalidValue(StringHelper::stringify($value));
        }

        $this->value = $this->cast($value);
    }

    /**
     * Cast input value to supported type by class
     * @param mixed $value input value
     * @return mixed casted value
     */
    protected function cast($value)
    {
        return $value;
    }

    /**
     * Validate input value
     * @param mixed $value
     * @return bool
     * @noinspection PhpUnusedParameterInspection
     * @codeCoverageIgnore
     */
    protected function validate($value): bool
    {
        return true;
    }

    /**
     * Throws exception for invalid input value
     * @param string|null $value stringified input value
     * @throws InvalidArgumentException
     */
    protected function throwExceptionForInvalidValue(?string $value): void
    {
        throw new InvalidArgumentException(
            $value === null
                ? sprintf('Unexpected value for "%s"', static::class)
                : sprintf('Unexpected value "%s" for "%s"', $value, static::class)
        );
    }

    /**
     * Returns inner value of VO
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Cast VO to string
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value();
    }

    /**
     * @inheritDoc
     * @return mixed|string
     */
    public function jsonSerialize()
    {
        return (string)$this;
    }
}
