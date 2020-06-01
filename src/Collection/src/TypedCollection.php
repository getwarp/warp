<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use InvalidArgumentException;
use LogicException;
use spaceonfire\Type\Type;
use spaceonfire\Type\TypeFactory;
use stdClass;

/**
 * Class `TypedCollection` allows you to create collection witch items are the same type.
 *
 * For testing scalar types provide type name from
 * [return values of `gettype` function](https://www.php.net/manual/en/function.gettype.php):
 *
 * ```php
 * $integers = new TypedCollection($items, 'integer');
 * $strings = new TypedCollection($items, 'string');
 * $floats = new TypedCollection($items, 'double');
 * ```
 *
 * If you want to test values are objects than provide class or interface name:
 *
 * ```php
 * $dateTime = new TypedCollection($items, \DateTime::class);
 * $jsonSerializable = new TypedCollection($items, \JsonSerializable::class);
 * ```
 *
 * @package spaceonfire\Collection
 */
final class TypedCollection extends AbstractCollectionDecorator
{
    /**
     * @var Type
     */
    protected $type;

    /**
     * TypedCollection constructor.
     * @param CollectionInterface|array|iterable|mixed $items
     * @param string|Type $type Scalar type name or Full qualified name of object class
     */
    public function __construct($items = [], $type = stdClass::class)
    {
        if (!$type instanceof Type) {
            if (!is_string($type)) {
                throw new InvalidArgumentException(sprintf(
                    'Argument $type expected to be a string or an instance of %s. Got: %s',
                    Type::class,
                    gettype($type)
                ));
            }

            $type = TypeFactory::create($type);
        }

        $this->type = $type;
        parent::__construct($items);

        foreach ($this->getIterator() as $item) {
            $this->checkType($item);
        }
    }

    /**
     * Check that item are the same type as collection requires
     * @param mixed $item
     * @return void
     */
    protected function checkType($item): void
    {
        if (!$this->type->check($item)) {
            throw new LogicException(static::class . ' accept only instances of ' . $this->type);
        }
    }

    /** {@inheritDoc} */
    protected function newStatic($items): CollectionInterface
    {
        return new self($items, $this->type);
    }

    /** {@inheritDoc} */
    public function offsetSet($offset, $value): void
    {
        $this->checkType($value);
        parent::offsetSet($offset, $value);
    }

    /** {@inheritDoc} */
    public function keys(): CollectionInterface
    {
        return $this->downgrade()->keys();
    }

    /** {@inheritDoc} */
    public function flip(): CollectionInterface
    {
        return $this->downgrade()->flip();
    }

    /**
     * {@inheritDoc}
     * Also collection will be downgraded
     */
    public function remap($from, $to): CollectionInterface
    {
        return $this->downgrade()->remap($from, $to);
    }

    /** {@inheritDoc} */
    public function groupBy($groupField, $preserveKeys = true): CollectionInterface
    {
        return $this->downgrade()
            ->groupBy($groupField, $preserveKeys)
            ->map(function (CollectionInterface $group) {
                return $this->newStatic($group->all());
            });
    }

    /**
     * {@inheritDoc}
     * Also collection will be downgraded
     */
    public function map(callable $callback): CollectionInterface
    {
        return $this->downgrade()->map($callback);
    }

    /** {@inheritDoc} */
    public function replace($item, $replacement, $strict = true): CollectionInterface
    {
        $this->checkType($item);
        $this->checkType($replacement);
        return $this->newStatic(parent::replace($item, $replacement, $strict)->all());
    }
}
