<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use function Symfony\Component\String\s;

abstract class AbstractEnumValue extends AbstractValueObject
{
    /**
     * @var array<class-string<static>,array<string,scalar>>
     */
    protected static array $cache = [];

    /**
     * Magic factory.
     * @param string $name
     * @param array{} $args
     * @return static
     */
    public static function __callStatic(string $name, array $args): self
    {
        return static::new(static::values()[$name]);
    }

    /**
     * Returns values array for this enum class.
     * @return array<string,scalar>
     */
    public static function values(): array
    {
        $class = static::class;

        try {
            if (!isset(self::$cache[$class])) {
                $reflection = new \ReflectionClass($class);

                self::$cache[$class] = [];

                foreach ($reflection->getReflectionConstants() as $constant) {
                    if (!$constant->isPublic()) {
                        continue;
                    }

                    if (!\is_scalar($constant->getValue())) {
                        throw new \LogicException(\sprintf(
                            '%s enum option %s expected to be scalar. Got: %s.',
                            $class,
                            $constant->getName(),
                            \get_debug_type($constant->getValue()),
                        ));
                    }

                    self::$cache[$class][self::keysFormatter($constant->getName())] = $constant->getValue();
                }
            }
        } catch (\ReflectionException $e) {
            return [];
        }

        return self::$cache[$class];
    }

    /**
     * @return scalar
     */
    public function jsonSerialize()
    {
        \assert(\is_scalar($this->value));
        return $this->value;
    }

    /**
     * Returns random value for this enum class.
     * @return scalar
     */
    public static function randomValue()
    {
        return static::values()[\array_rand(static::values())];
    }

    /**
     * Creates new enum VO with random value.
     * @return static
     */
    public static function random(): self
    {
        return static::new(static::randomValue());
    }

    protected static function validate($value): void
    {
        if (!\is_scalar($value)) {
            throw new \InvalidArgumentException('%s enum accepts only scalar values.');
        }

        if (!\in_array($value, static::values(), true)) {
            throw new \InvalidArgumentException(\sprintf(
                'The value is out of %s enum values: %s. Got: %s.',
                static::class,
                \implode(', ', static::values()),
                $value,
            ));
        }
    }

    private static function keysFormatter(string $key): string
    {
        return s($key)->lower()->replace('_', ' ')->title(true)->camel()->toString();
    }
}
