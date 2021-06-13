<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;
use spaceonfire\Type\Factory\BuiltinTypeFactory;
use Webmozart\Assert\Assert;

final class BuiltinType implements TypeInterface
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

    public const SCALAR_TYPES = [
        self::INT => self::INT,
        self::FLOAT => self::FLOAT,
        self::STRING => self::STRING,
        self::BOOL => self::BOOL,
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
        Assert::oneOf($type, self::ALL);

        if (false === $strict && !isset(self::SCALAR_TYPES[$type])) {
            $strict = true;
            trigger_error(sprintf('Type "%s" cannot be non-strict. $strict argument overridden.', $type));
        }

        $this->type = $type;
        $this->strict = $strict;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->type;
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
                    if ($this->strict) {
                        Assert::boolean($value);
                    } else {
                        Assert::scalar($value);
                    }
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

            case self::FLOAT:
                return (float)$value;

            case self::STRING:
                return (string)$value;

            case self::BOOL:
                return (bool)$value;

            case self::NULL:
                return null;

            default:
                return $value;
        }
    }

    /**
     * @param string $type
     * @return bool
     * @deprecated use dynamic type factory instead. This method will be removed in next major release.
     * @see Factory\TypeFactoryInterface
     */
    public static function supports(string $type): bool
    {
        return (new BuiltinTypeFactory())->supports($type);
    }

    /**
     * @param string $type
     * @param bool $strict
     * @return self
     * @deprecated use dynamic type factory instead. This method will be removed in next major release.
     * @see Factory\TypeFactoryInterface
     */
    public static function create(string $type, bool $strict = true): TypeInterface
    {
        return (new BuiltinTypeFactory($strict))->make($type);
    }
}
