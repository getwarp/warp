<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use InvalidArgumentException;
use Jawira\CaseConverter\Convert;
use ReflectionClass;

abstract class EnumValue extends BaseValueObject
{
    protected static $cache = [];

    public function __construct($value)
    {
        $this->ensureIsBetweenAcceptedValues($value);
        parent::__construct($value);
    }

    private function ensureIsBetweenAcceptedValues($value): void
    {
        if (!in_array($value, static::values(), true)) {
            $this->throwExceptionForInvalidValue($value);
        }
    }

    public function equals(EnumValue $other): bool
    {
        /** @noinspection TypeUnsafeComparisonInspection PhpNonStrictObjectEqualityInspection */
        return $other == $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return (string)$this;
    }

    protected function throwExceptionForInvalidValue($value)
    {
        throw new InvalidArgumentException(sprintf('Value "%s" is not allowed for "%s"', $value, static::class));
    }

    public static function __callStatic(string $name, $args)
    {
        return new static(self::values()[$name]);
    }

    public static function randomValue()
    {
        return self::values()[array_rand(self::values())];
    }

    public static function random(): self
    {
        return new static(self::randomValue());
    }

    public static function values(): array
    {
        $class = static::class;

        if (!isset(self::$cache[$class])) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $reflected = new ReflectionClass($class);
            self::$cache[$class] = [];
            foreach ($reflected->getConstants() as $key => $value) {
                self::$cache[$class][self::keysFormatter($key)] = $value;
            }
        }

        return self::$cache[$class];
    }

    private static function keysFormatter(string $key): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (new Convert(strtolower($key)))->toCamel();
    }
}
