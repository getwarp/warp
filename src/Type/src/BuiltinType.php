<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class BuiltinType implements Type
{
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

    private const SUPPORT_NO_STRICT = [
        self::INT => true,
        self::FLOAT => true,
        self::STRING => true,
    ];

    /**
     * @var string
     */
    private $type;
    /**
     * @var bool
     */
    private $strict;

    /**
     * BuiltinType constructor.
     * @param string $type
     * @param bool $strict
     */
    public function __construct(string $type, bool $strict = true)
    {
        if (!self::supports($type)) {
            throw new InvalidArgumentException(sprintf('Type "%s" is not supported by %s', $type, __CLASS__));
        }

        $this->type = self::prepareType($type);

        if ($strict === false && !isset(self::SUPPORT_NO_STRICT[$this->type])) {
            $strict = true;
            trigger_error(sprintf('Type "%s" cannot be non-strict. $strict argument overridden.', $this->type));
        }

        $this->strict = $strict;
    }

    /**
     * @inheritDoc
     */
    public function check($value): bool
    {
        try {
            switch ($this->type) {
                case self::INT:
                    if ($this->strict) {
                        Assert::integer($value);
                    } else {
                        Assert::integerish($value);
                    }
                    break;

                case self::FLOAT:
                    if ($this->strict) {
                        Assert::float($value);
                    } else {
                        Assert::numeric($value);
                    }
                    break;

                case self::STRING:
                    if ($this->strict) {
                        Assert::string($value);
                    } elseif (is_object($value)) {
                        Assert::methodExists($value, '__toString');
                    } else {
                        Assert::scalar($value);
                    }
                    break;

                case self::BOOL:
                    Assert::boolean($value);
                    break;

                case self::RESOURCE:
                    Assert::resource($value);
                    break;

                case self::OBJECT:
                    Assert::object($value);
                    break;

                case self::ARRAY:
                    Assert::isArray($value);
                    break;

                case self::NULL:
                    Assert::null($value);
                    break;

                case self::CALLABLE:
                    Assert::isCallable($value);
                    break;

                case self::ITERABLE:
                    Assert::isIterable($value);
                    break;
            }

            return true;
        } catch (InvalidArgumentException $exception) {
            return false;
        }
    }

    /**
     * Cast value to current type
     * @param mixed $value
     * @return mixed
     */
    public function cast($value)
    {
        switch ($this->type) {
            case self::INT:
                return (int)$value;
                break;

            case self::FLOAT:
                return (float)$value;
                break;

            case self::STRING:
                return (string)$value;
                break;

            default:
                return $value;
                break;
        }
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->type;
    }

    private static function prepareType(string $type): string
    {
        $type = strtolower($type);

        if (strpos($type, 'resource') === 0) {
            $type = self::RESOURCE;
        }

        $map = [
            'boolean' => self::BOOL,
            'integer' => self::INT,
            'double' => self::FLOAT,
        ];

        return $map[$type] ?? $type;
    }

    /**
     * @inheritDoc
     */
    public static function supports(string $type): bool
    {
        $type = self::prepareType($type);

        $supported = [
            self::INT => true,
            self::FLOAT => true,
            self::STRING => true,
            self::BOOL => true,
            self::RESOURCE => true,
            self::OBJECT => true,
            self::ARRAY => true,
            self::NULL => true,
            self::CALLABLE => true,
            self::ITERABLE => true,
        ];

        return array_key_exists($type, $supported);
    }

    /**
     * @inheritDoc
     */
    public static function create(string $type, bool $strict = true): Type
    {
        return new self($type, $strict);
    }
}
