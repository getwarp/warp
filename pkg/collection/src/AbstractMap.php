<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use spaceonfire\Collection\Iterator\ArrayIterator;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\DisjunctionType;
use spaceonfire\Type\MixedType;
use spaceonfire\Type\TypeInterface;

/**
 * @template K of array-key
 * @template V
 * @implements MapInterface<K,V>
 */
abstract class AbstractMap implements MapInterface
{
    /**
     * @var ArrayIterator<K,V>
     */
    protected ArrayIterator $source;

    protected TypeInterface $valueType;

    private static ?TypeInterface $keyType = null;

    /**
     * @param ArrayIterator<K,V> $source
     * @param TypeInterface $valueType
     */
    protected function __construct(ArrayIterator $source, TypeInterface $valueType)
    {
        $this->source = $source;
        $this->valueType = $valueType;
    }

    public function set($key, $element): void
    {
        $this->assertKeyType($key);
        $this->assertValueType($element);
        $this->source->offsetSet($key, $element);
    }

    public function unset($key): void
    {
        $this->assertKeyType($key);

        if (!$this->source->offsetExists($key)) {
            return;
        }

        $this->source->offsetUnset($key);
    }

    public function has($key): bool
    {
        $this->assertKeyType($key);
        return $this->source->offsetExists($key);
    }

    public function get($key)
    {
        $this->assertKeyType($key);

        if (!$this->source->offsetExists($key)) {
            return null;
        }

        return $this->source->offsetGet($key);
    }

    public function applyOperation(OperationInterface $operation): MapInterface
    {
        return $this->withSource(
            new ArrayIterator($operation->apply($this->source)),
            $operation instanceof AlterValueTypeOperationInterface ? MixedType::new() : null
        );
    }

    public function merge(iterable $other, iterable ...$others): MapInterface
    {
        return $this->applyOperation(new Operation\MergeOperation([$other, ...$others], true));
    }

    public function values(): CollectionInterface
    {
        return $this->makeCollection((new Operation\ValuesOperation())->apply($this->source), $this->valueType);
    }

    public function keys(): CollectionInterface
    {
        return $this->makeCollection((new Operation\KeysOperation())->apply($this->source), self::getKeyType());
    }

    public function firstKey()
    {
        return \array_key_first($this->source->getArrayCopy());
    }

    public function lastKey()
    {
        return \array_key_last($this->source->getArrayCopy());
    }

    public function getIterator(): \Traversable
    {
        return $this->source;
    }

    public function count(): int
    {
        return $this->source->count();
    }

    /**
     * @return array<K,V>
     */
    public function jsonSerialize(): array
    {
        return $this->source->getArrayCopy();
    }

    /**
     * @template T
     * @param ArrayIterator<K,T> $source
     * @param TypeInterface|null $valueType
     * @return static<K,T>
     */
    abstract protected function withSource(ArrayIterator $source, ?TypeInterface $valueType = null): MapInterface;

    /**
     * @template T
     * @param iterable<int,T> $elements
     * @param TypeInterface|null $valueType
     * @return CollectionInterface<T>
     */
    abstract protected function makeCollection(
        iterable $elements = [],
        ?TypeInterface $valueType = null
    ): CollectionInterface;

    /**
     * @param V ...$values
     */
    protected function assertValueType(...$values): void
    {
        foreach ($values as $value) {
            if ($this->valueType->check($value)) {
                continue;
            }

            throw new \LogicException(\sprintf(
                'Map accepts only elements of type %s. Got: %s.',
                $this->valueType,
                \get_debug_type($value)
            ));
        }
    }

    /**
     * @param K ...$keys
     */
    protected function assertKeyType(...$keys): void
    {
        $keyType = self::getKeyType();

        foreach ($keys as $key) {
            if ($keyType->check($key)) {
                continue;
            }

            throw new \LogicException(\sprintf(
                'Map accepts only keys of type %s. Got: %s.',
                $keyType,
                \get_debug_type($key)
            ));
        }
    }

    /**
     * @return TypeInterface array-key type checker
     */
    final protected static function getKeyType(): TypeInterface
    {
        return self::$keyType ??= DisjunctionType::new(
            BuiltinType::new(BuiltinType::INT),
            BuiltinType::new(BuiltinType::STRING),
        );
    }
}
