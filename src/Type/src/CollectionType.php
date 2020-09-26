<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;
use Traversable;

final class CollectionType implements Type
{
    /**
     * @var Type
     */
    private $valueType;
    /**
     * @var Type|null
     */
    private $keyType;
    /**
     * @var Type
     */
    private $iterableType;

    /**
     * CollectionType constructor.
     * @param Type $valueType
     * @param Type|null $keyType
     * @param Type|null $iterableType
     */
    public function __construct(Type $valueType, ?Type $keyType = null, ?Type $iterableType = null)
    {
        $this->valueType = $valueType;
        $this->keyType = $keyType;
        $this->iterableType = $iterableType ?? new BuiltinType(BuiltinType::ITERABLE);
    }

    /**
     * @inheritDoc
     */
    public function check($value): bool
    {
        if (!$this->iterableType->check($value)) {
            return false;
        }

        foreach ($value as $k => $v) {
            if (!$this->valueType->check($v)) {
                return false;
            }

            if ($this->keyType !== null && !$this->keyType->check($k)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        if ($this->iterableType instanceof InstanceOfType || $this->keyType !== null) {
            return $this->iterableType . '<' . implode(',', array_filter([$this->keyType, $this->valueType])) . '>';
        }

        return $this->valueType . '[]';
    }

    /**
     * @inheritDoc
     */
    public static function supports(string $type): bool
    {
        $typeParts = self::parseType($type);

        if ($typeParts === null) {
            return false;
        }

        if (isset($typeParts['iterable'])) {
            if (InstanceOfType::supports($typeParts['iterable'])) {
                if (
                    $typeParts['iterable'] !== Traversable::class &&
                    !is_subclass_of($typeParts['iterable'], Traversable::class)
                ) {
                    return false;
                }
            } else {
                if (!in_array($typeParts['iterable'], ['array', 'iterable'], true)) {
                    return false;
                }
            }
        }

        if (!isset($typeParts['value'])) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function create(string $type): Type
    {
        if (!self::supports($type)) {
            throw new InvalidArgumentException(sprintf('Type "%s" is not supported by %s', $type, __CLASS__));
        }

        /** @var array $parsed */
        $parsed = self::parseType($type);

        $parsed['value'] = TypeFactory::create($parsed['value']);
        $parsed['key'] = $parsed['key'] ? TypeFactory::create($parsed['key']) : null;
        $parsed['iterable'] = $parsed['iterable'] ? TypeFactory::create($parsed['iterable']) : null;

        return new self($parsed['value'], $parsed['key'], $parsed['iterable']);
    }

    /**
     * @param string $type
     * @return array|null
     */
    private static function parseType(string $type): ?array
    {
        $result = [
            'iterable' => null,
            'key' => null,
            'value' => null,
        ];

        if (strpos($type, '[]') === strlen($type) - 2) {
            $result['value'] = substr($type, 0, -2) ?: null;
            return $result;
        }

        if (
            (0 < $openPos = strpos($type, '<')) &&
            (strpos($type, '>') === strlen($type) - 1)
        ) {
            $result['iterable'] = substr($type, 0, $openPos);
            [$key, $value] = array_map('trim', explode(',', substr($type, $openPos + 1, -1))) + [null, null];

            if (!$value && !$key) {
                return null;
            }

            if ($value === null) {
                $value = $key;
                $key = null;
            }

            $result['key'] = $key ?: null;
            $result['value'] = $value ?: null;

            return $result;
        }

        return null;
    }
}
