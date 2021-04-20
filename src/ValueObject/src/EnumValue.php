<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use InvalidArgumentException;
use Jawira\CaseConverter\Convert;
use ReflectionClass;
use ReflectionException;
use spaceonfire\ValueObject\Helpers\StringHelper;

abstract class EnumValue extends BaseValueObject
{
    /**
     * @var string[][]
     */
    protected static $cache = [];

    /**
     * Support for magic methods
     * @param string $name
     * @param array $args
     * @return static
     */
    public static function __callStatic(string $name, $args)
    {
        return new static(self::values()[$name]);
    }

    /**
     * Returns random value for this enum class.
     * @return mixed|string
     */
    public static function randomValue()
    {
        return self::values()[array_rand(self::values())];
    }

    /**
     * Creates new enum VO with random value.
     * @return static
     */
    public static function random(): self
    {
        return new static(self::randomValue());
    }

    /**
     * Returns values array for this enum class.
     * @return mixed[]
     */
    public static function values(): array
    {
        $class = static::class;

        try {
            if (!isset(self::$cache[$class])) {
                $reflected = new ReflectionClass($class);
                self::$cache[$class] = [];
                foreach ($reflected->getReflectionConstants() as $constant) {
                    if (!$constant->isPublic()) {
                        continue;
                    }

                    self::$cache[$class][self::keysFormatter($constant->getName())] = $constant->getValue();
                }
            }
        } catch (ReflectionException $e) {
            return [];
        }

        return self::$cache[$class];
    }

    /**
     * @inheritDoc
     */
    protected function validate($value): bool
    {
        return in_array($value, static::values(), true);
    }

    /**
     * @inheritDoc
     */
    protected function throwExceptionForInvalidValue(?string $value): void
    {
        if (null !== $value) {
            $valuesAsString = StringHelper::stringify(array_values(static::values()));
            $suggestion = StringHelper::getSuggestion(static::values(), $value);

            throw new InvalidArgumentException(
                sprintf('The value is outside the allowable range of values: %s. Got: \'%s\'', $valuesAsString, $value)
                . ($suggestion ? ', did you mean \'' . $suggestion . '\'?' : '')
            );
        }

        parent::throwExceptionForInvalidValue($value);
    }

    private static function keysFormatter(string $key): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (new Convert(strtolower($key)))->toCamel();
    }
}
