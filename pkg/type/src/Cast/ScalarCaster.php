<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

use spaceonfire\Type\BuiltinType;

final class ScalarCaster implements CasterInterface
{
    private const SCALAR_TYPES = [
        BuiltinType::INT => BuiltinType::INT,
        BuiltinType::FLOAT => BuiltinType::FLOAT,
        BuiltinType::STRING => BuiltinType::STRING,
        BuiltinType::BOOL => BuiltinType::BOOL,
    ];

    private BuiltinType $type;

    public function __construct(BuiltinType $type)
    {
        if (!isset(self::SCALAR_TYPES[(string)$type])) {
            throw new \InvalidArgumentException(\sprintf('Non scalar type (%s) given to ScalarCaster.', $type));
        }

        $this->type = $type;
    }

    public function accepts($value): bool
    {
        switch ((string)$this->type) {
            case BuiltinType::INT:
                return false !== \filter_var($value, \FILTER_VALIDATE_INT);
            case BuiltinType::FLOAT:
                return false !== \filter_var($value, \FILTER_VALIDATE_FLOAT);
            case BuiltinType::STRING:
                return null === $value
                    || \is_scalar($value)
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
