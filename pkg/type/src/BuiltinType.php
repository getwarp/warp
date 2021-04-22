<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use spaceonfire\Common\Factory\SingletonStorageTrait;
use spaceonfire\Common\Factory\StaticConstructorInterface;

/**
 * @method static self int()
 * @method static self float()
 * @method static self string()
 * @method static self bool()
 * @method static self resource()
 * @method static self object()
 * @method static self array()
 * @method static self null()
 * @method static self callable()
 * @method static self iterable()
 */
final class BuiltinType implements TypeInterface, StaticConstructorInterface
{
    use SingletonStorageTrait;

    public const INT = 'int';

    public const FLOAT = 'float';

    public const STRING = 'string';

    public const BOOL = 'bool';

    public const RESOURCE = 'resource';

    public const OBJECT = 'object';

    public const ARRAY = 'array';

    public const NULL = 'null';

    public const CALLABLE = 'callable';

    public const ITERABLE = 'iterable';

    public const ALL = [
        self::INT,
        self::FLOAT,
        self::STRING,
        self::BOOL,
        self::RESOURCE,
        self::OBJECT,
        self::ARRAY,
        self::NULL,
        self::CALLABLE,
        self::ITERABLE,
    ];

    private string $type;

    private function __construct(string $type)
    {
        $this->type = $type;

        self::singletonAttach($this);
    }

    public function __destruct()
    {
        self::singletonDetach($this);
    }

    public function __toString(): string
    {
        return $this->type;
    }

    /**
     * Magic factory.
     * @param string $name
     * @param array{} $arguments
     * @return self
     */
    public static function __callStatic(string $name, array $arguments): self
    {
        return self::new($name);
    }

    public static function new(string $type): self
    {
        if (!\in_array($type, self::ALL, true)) {
            throw new \InvalidArgumentException(\sprintf(
                'Argument #1 ($type) should be one of: %s. Got: %s.',
                \implode(', ', self::ALL),
                $type,
            ));
        }

        return self::singletonFetch($type) ?? new self($type);
    }

    public function check($value): bool
    {
        switch ($this->type) {
            case self::INT:
                return \is_int($value);

            case self::FLOAT:
                return \is_float($value);

            case self::STRING:
                return \is_string($value);

            case self::BOOL:
                return \is_bool($value);

            case self::RESOURCE:
                return \is_resource($value);

            case self::OBJECT:
                return \is_object($value);

            case self::ARRAY:
                return \is_array($value);

            case self::NULL:
                return null === $value;

            case self::CALLABLE:
                return \is_callable($value);

            case self::ITERABLE:
                return \is_iterable($value);
        }

        return false;
    }

    /**
     * @param string|self $value
     * @return string
     */
    protected static function singletonKey($value): string
    {
        return (string)$value;
    }
}
