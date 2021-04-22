<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use spaceonfire\Common\Factory\SingletonStorageTrait;
use spaceonfire\Common\Factory\StaticConstructorInterface;

final class CollectionType implements TypeInterface, StaticConstructorInterface
{
    use SingletonStorageTrait;

    private TypeInterface $iterableType;

    private TypeInterface $valueType;

    private ?TypeInterface $keyType;

    private function __construct(
        TypeInterface $iterableType,
        TypeInterface $valueType,
        ?TypeInterface $keyType = null
    ) {
        $this->iterableType = $iterableType;
        $this->valueType = $valueType;
        $this->keyType = $keyType;

        self::singletonAttach($this);
    }

    public function __destruct()
    {
        self::singletonDetach($this);
    }

    public function __toString(): string
    {
        if (
            $this->iterableType instanceof InstanceOfType ||
            $this->valueType instanceof AbstractAggregatedType ||
            null !== $this->keyType
        ) {
            return $this->iterableType . '<' . \implode(',', \array_filter([$this->keyType, $this->valueType])) . '>';
        }

        return $this->valueType . '[]';
    }

    public static function new(
        TypeInterface $valueType,
        ?TypeInterface $keyType = null,
        ?TypeInterface $iterableType = null
    ): self {
        $iterableType ??= BuiltinType::new(BuiltinType::ITERABLE);

        return self::singletonFetch(self::singletonKey([$iterableType, $valueType, $keyType]))
            ?? new self($iterableType, $valueType, $keyType);
    }

    public function check($value): bool
    {
        if (!$this->iterableType->check($value)) {
            return false;
        }

        foreach ($value as $k => $v) {
            if (!$this->valueType->check($v)) {
                return false;
            }

            if (null !== $this->keyType && !$this->keyType->check($k)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param self|array{TypeInterface,TypeInterface,TypeInterface|null} $value
     * @return string
     */
    protected static function singletonKey($value): string
    {
        if ($value instanceof self) {
            $iterableType = $value->iterableType;
            $valueType = $value->valueType;
            $keyType = $value->keyType;
        } else {
            [$iterableType, $valueType, $keyType] = $value;
        }

        return \implode(':', [$iterableType, $valueType, $keyType]);
    }
}
