<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\TypeInterface;

final class ScalarCaster implements CasterInterface
{
    private const SCALAR_TYPES = [
        BuiltinType::INT => BuiltinType::INT,
        BuiltinType::FLOAT => BuiltinType::FLOAT,
        BuiltinType::STRING => BuiltinType::STRING,
        BuiltinType::BOOL => BuiltinType::BOOL,
    ];

    private BuiltinType $type;

    public function __construct(TypeInterface $type)
    {
        if (!self::isScalar($type)) {
            throw new \InvalidArgumentException(\sprintf('Non scalar type (%s) given to ScalarCaster.', $type));
        }

        \assert($type instanceof BuiltinType);
        $this->type = $type;
    }

    public static function isScalar(TypeInterface $type): bool
    {
        return $type instanceof BuiltinType && isset(self::SCALAR_TYPES[(string)$type]);
    }

    public function accepts($value): bool
    {
        switch ((string)$this->type) {
            case BuiltinType::INT:
                return false !== \filter_var($value, \FILTER_VALIDATE_INT);
            case BuiltinType::FLOAT:
                return false !== \filter_var($value, \FILTER_VALIDATE_FLOAT);
            case BuiltinType::STRING:
                return \is_string($value)
                    || \is_numeric($value)
                    || (\is_object($value) && \method_exists($value, '__toString'));
            case BuiltinType::BOOL:
                return null !== \filter_var($value, \FILTER_VALIDATE_BOOL, \FILTER_NULL_ON_FAILURE);
        }

        return false;
    }

    public function cast($value)
    {
        if (!$this->accepts($value)) {
            throw new \InvalidArgumentException(\sprintf(
                'Given value (%s) cannot be casted to type %s.',
                \get_debug_type($value),
                $this->type,
            ));
        }

        switch ((string)$this->type) {
            case BuiltinType::INT:
                return \filter_var($value, \FILTER_VALIDATE_INT);
            case BuiltinType::FLOAT:
                return \filter_var($value, \FILTER_VALIDATE_FLOAT);
            case BuiltinType::STRING:
                return (string)$value;
            case BuiltinType::BOOL:
                return \filter_var($value, \FILTER_VALIDATE_BOOL);
        }

        return $value;
    }
}
