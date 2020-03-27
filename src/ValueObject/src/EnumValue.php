<?php

/**
 * @phpcs:disable PSR12.Classes.ClosingBrace.StatementAfter
 */

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use InvalidArgumentException;
use Jawira\CaseConverter\Convert;
use ReflectionClass;
use spaceonfire\ValueObject\Helpers\StringHelper;

abstract class EnumValue extends BaseValueObject
{
    protected static $cache = [];

    protected function validate($value): bool
    {
        return in_array($value, static::values(), true);
    }

    protected function throwExceptionForInvalidValue(?string $value): void
    {
        if ($value !== null) {
            $valuesAsString = StringHelper::stringify(array_values(static::values()));
            $suggestion = StringHelper::getSuggestion(static::values(), $value);

            throw new InvalidArgumentException(
                sprintf('The value is outside the allowable range of values: %s. Got: \'%s\'', $valuesAsString, $value)
                . ($suggestion ? ', did you mean \'' . $suggestion . '\'?' : '')
            );
        }

        parent::throwExceptionForInvalidValue($value);
    } // @codeCoverageIgnore

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
