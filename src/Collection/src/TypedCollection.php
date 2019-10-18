<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use RuntimeException;
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
class TypedCollection extends BaseCollection
{
    /**
     * @var string
     */
    protected $type;

    /**
     * TypedCollection constructor.
     * @param array $items
     * @param string $type Scalar type name or Full qualified name of object class
     */
    public function __construct($items = [], string $type = stdClass::class)
    {
        $this->type = $type;
        parent::__construct($items);
    }

    /** {@inheritDoc} */
    protected function getItems($items): array
    {
        $result = parent::getItems($items);
        foreach ($result as $item) {
            $type = gettype($item);

            if (($type === 'object') && !class_exists($this->type)) {
                throw new RuntimeException('Class ' . $this->type . ' does not exist');
            }

            if (($type === 'object' && !($item instanceof $this->type)) ||
                ($type !== 'object' && $type !== $this->type)) {
                throw new RuntimeException(static::class . ' accept only instances of ' . $this->type);
            }
        }
        return $result;
    }
}
