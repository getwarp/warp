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

    public function __construct($value)
    {
        if (!$this->validate($value)) {
            $this->throwExceptionForInvalidValue(StringHelper::stringify($value));
        }

        $this->value = $this->cast($value);
    }

    protected function cast($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return bool
     * @noinspection PhpUnusedParameterInspection
     * @codeCoverageIgnore
     */
    protected function validate($value): bool
    {
        return true;
    }

    protected function throwExceptionForInvalidValue(?string $value): void
    {
        throw new InvalidArgumentException(
            $value === null
                ? sprintf('Unexpected value for "%s"', static::class)
                : sprintf('Unexpected value "%s" for "%s"', $value, static::class)
        );
    }

    public function value()
    {
        return $this->value;
    }

    /**
     * Cast value object to string
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value();
    }
}
