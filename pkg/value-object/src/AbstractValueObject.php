<?php

declare(strict_types=1);

namespace Warp\ValueObject;

use Warp\Common\Factory\SingletonStorageTrait;
use Warp\Common\Factory\StaticConstructorInterface;

/**
 * @template T of scalar|\Stringable
 */
abstract class AbstractValueObject implements \Stringable, \JsonSerializable, StaticConstructorInterface
{
    use SingletonStorageTrait;

    /**
     * @var T
     */
    protected $value;

    /**
     * @param T $value
     */
    final private function __construct($value)
    {
        if (!self::checkValueType($value)) {
            throw new \InvalidArgumentException(
                \sprintf('Expected value to be of type scalar or \Stringable. Got: %s.', \get_debug_type($value))
            );
        }

        $this->value = $value;

        self::singletonAttach($this);
    }

    final public function __destruct()
    {
        self::singletonDetach($this);
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    /**
     * VO Constructor.
     * @param mixed $value
     * @return static
     */
    final public static function new($value): self
    {
        static::validate($value);

        $value = static::cast($value);

        return self::singletonFetch(self::singletonKey($value)) ?? new static($value);
    }

    /**
     * Returns inner value of VO
     * @return T
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Checks that current VO is equals to given one.
     * @param static<T> $other
     * @return bool
     */
    public function equals($other): bool
    {
        return $this === $other;
    }

    /**
     * Validate input value
     * @param mixed $value
     */
    abstract protected static function validate($value): void;

    /**
     * Cast input value to supported type by class
     * @param mixed $value input value
     * @return T casted value
     */
    protected static function cast($value)
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    final protected static function checkValueType($value): bool
    {
        if (\is_scalar($value)) {
            return true;
        }

        if (\is_object($value) && \method_exists($value, '__toString')) {
            return true;
        }

        return false;
    }

    /**
     * @param static|scalar|\Stringable $value
     * @return string
     */
    final protected static function singletonKey($value): string
    {
        return \serialize(self::toScalar($value instanceof self ? $value->value : $value));
    }

    /**
     * @param scalar|\Stringable $value
     * @return scalar
     */
    private static function toScalar($value)
    {
        if (\is_scalar($value)) {
            return $value;
        }

        if (\is_object($value) && \method_exists($value, '__toString')) {
            return (string)$value;
        }

        throw new \RuntimeException(\sprintf('Cannot convert %s to scalar.', \get_debug_type($value)));
    }
}
