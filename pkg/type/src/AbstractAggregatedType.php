<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use spaceonfire\Common\Factory\SingletonStorageTrait;
use spaceonfire\Common\Factory\StaticConstructorInterface;

/**
 * @implements \IteratorAggregate<TypeInterface>
 */
abstract class AbstractAggregatedType implements TypeInterface, StaticConstructorInterface, \IteratorAggregate
{
    use SingletonStorageTrait;

    public const DELIMITER = '';

    /**
     * @var TypeInterface[]
     */
    protected array $types;

    /**
     * @param TypeInterface[] $types
     */
    final private function __construct(array $types)
    {
        $this->types = $types;

        self::singletonAttach($this);
    }

    final public function __destruct()
    {
        self::singletonDetach($this);
    }

    public function __toString(): string
    {
        return \implode(static::DELIMITER, $this->types);
    }

    /**
     * @param TypeInterface ...$types
     * @return static
     */
    public static function new(TypeInterface ...$types): self
    {
        if (1 !== \strlen(static::DELIMITER)) {
            throw new \LogicException(\sprintf('%s::DELIMITER should be 1 symbol string.', static::class));
        }

        $types = \array_values(self::prepareTypes($types));

        if (2 > \count($types)) {
            throw new \InvalidArgumentException(\sprintf(
                '%s::new() requires at least 2 different types, %d given.',
                static::class,
                \count($types),
            ));
        }

        return self::singletonFetch(self::singletonKey($types)) ?? new static($types);
    }

    /**
     * @return \Traversable<TypeInterface>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->types);
    }

    /**
     * @param iterable<TypeInterface> $value
     * @return string
     */
    final protected static function singletonKey($value): string
    {
        return \implode(
            static::DELIMITER,
            $value instanceof \Traversable ? \iterator_to_array($value, false) : (array)$value
        );
    }

    /**
     * @param TypeInterface[] $types
     * @return TypeInterface[]
     */
    private static function prepareTypes(iterable $types): array
    {
        $output = [];

        foreach ($types as $type) {
            if ($type instanceof static) {
                $output += self::prepareTypes($type->types);
                continue;
            }

            $output[(string)$type] = $type;
        }

        return $output;
    }
}
