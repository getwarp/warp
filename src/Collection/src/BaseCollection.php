<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use ArrayIterator;
use BadMethodCallException;
use Closure;
use InvalidArgumentException;
use JsonSerializable;
use Traversable;

/**
 * Class BaseCollection
 * @package spaceonfire\Collection
 */
abstract class BaseCollection implements CollectionInterface, JsonSerializable
{
    /**
     * @var array The items contained in the collection.
     */
    protected $items = [];

    /**
     * BaseCollection constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = $this->getItems($items);
    }

    /**
     * Results array of items.
     * @param mixed $items
     * @return array
     */
    protected function getItems($items): array
    {
        if (is_array($items)) {
            return $items;
        }

        if ($items instanceof self) {
            return $items->all();
        }

        if ($items instanceof JsonSerializable) {
            return $items->jsonSerialize();
        }

        if ($items instanceof Traversable) {
            return iterator_to_array($items);
        }

        return (array)$items;
    }

    /** {@inheritDoc} */
    public function all(): array
    {
        return $this->items;
    }

    /** {@inheritDoc} */
    public function each(callable $callback)
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * Calculate the sum of a field of the models in the collection.
     * @param string|Closure|array|null $field the name of the field to calculate.
     * This will be passed to [[ArrayHelper::getValue()]].
     * @return int|float the calculated sum.
     */
    public function sum($field = null)
    {
        return $this->reduce(static function ($accum, $item) use ($field) {
            $value = $field === null ? $item : ArrayHelper::getValue($item, $field, 0);

            if (!is_numeric($value)) {
                throw new BadMethodCallException('Non-numeric value used in ' . __METHOD__);
            }

            return $accum + $value;
        }, 0);
    }

    /** {@inheritDoc} */
    public function reduce(callable $callback, $initialValue = null)
    {
        return array_reduce($this->all(), $callback, $initialValue);
    }

    /**
     * Calculate the maximum value of a field of the models in the collection.
     * @param string|Closure|array $field the name of the field to calculate.
     * This will be passed to [[ArrayHelper::getValue()]].
     * @return int|float|null the calculated maximum value. `null` if the collection is empty.
     */
    public function max($field = null)
    {
        return $this->reduce(static function ($accum, $item) use ($field) {
            $value = $field === null ? $item : ArrayHelper::getValue($item, $field, 0);

            if (!is_numeric($value)) {
                throw new BadMethodCallException('Non-numeric value used in ' . __METHOD__);
            }

            if ($accum === null) {
                return $value;
            }

            return $value > $accum ? $value : $accum;
        });
    }

    /**
     * Calculate the minimum value of a field of the models in the collection
     * @param string|Closure|array $field the name of the field to calculate.
     * This will be passed to [[ArrayHelper::getValue()]].
     * @return int|float|null the calculated minimum value. `null` if the collection is empty.
     */
    public function min($field = null)
    {
        return $this->reduce(static function ($accum, $item) use ($field) {
            $value = $field === null ? $item : ArrayHelper::getValue($item, $field, 0);

            if (!is_numeric($value)) {
                throw new BadMethodCallException('Non-numeric value used in ' . __METHOD__);
            }

            if ($accum === null) {
                return $value;
            }

            return $value < $accum ? $value : $accum;
        });
    }

    /** {@inheritDoc} */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /** {@inheritDoc} */
    public function count()
    {
        return count($this->items);
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection with sorted data is returned.
     * @see http://php.net/manual/en/function.asort.php
     * @see http://php.net/manual/en/function.arsort.php
     */
    public function sort($direction = SORT_ASC, $sortFlag = SORT_REGULAR)
    {
        $items = $this->all();
        if ($direction === SORT_ASC) {
            asort($items, $sortFlag);
        } else {
            arsort($items, $sortFlag);
        }
        return new static($items);
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection with sorted data is returned.
     * @see http://php.net/manual/en/function.ksort.php
     * @see http://php.net/manual/en/function.krsort.php
     */
    public function sortByKey($direction = SORT_ASC, $sortFlag = SORT_REGULAR)
    {
        $items = $this->all();
        if ($direction === SORT_ASC) {
            ksort($items, $sortFlag);
        } else {
            krsort($items, $sortFlag);
        }
        return new static($items);
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection with sorted data is returned.
     * @see http://php.net/manual/en/function.natsort.php
     * @see http://php.net/manual/en/function.natcasesort.php
     */
    public function sortNatural($caseSensitive = false)
    {
        $items = $this->all();
        if ($caseSensitive) {
            natsort($items);
        } else {
            natcasesort($items);
        }
        return new static($items);
    }

    /**
     * {@inheritDoc}
     *
     * Note that keys will not be preserved by this method.
     *
     * This method uses `ArrayHelper::multisort()` on the collection data.
     *
     * The original collection will not be changed, a new collection with sorted data is returned.
     *
     * @throws InvalidArgumentException if the $direction or $sortFlag parameters do not have
     * correct number of elements as that of $key.
     * @see ArrayHelper::multisort()
     */
    public function sortBy($key, $direction = SORT_ASC, $sortFlag = SORT_REGULAR)
    {
        $items = $this->all();
        ArrayHelper::multisort($items, $key, $direction, $sortFlag);
        return new static($items);
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function reverse()
    {
        return new static(array_reverse($this->all(), true));
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function values()
    {
        return new static(array_values($this->all()));
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function keys()
    {
        return new static(array_keys($this->all()));
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function flip()
    {
        return new static(array_flip($this->all()));
    }

    /**
     * {@inheritDoc}
     *
     * Data in this collection will be overwritten if non-integer keys exist in the merged collection.
     *
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function merge(...$collections)
    {
        return new static(array_merge(
            $this->all(),
            ...array_map(function ($collection) {
                return $this->getItems($collection);
            }, $collections)
        ));
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function indexBy($key)
    {
        return $this->remap($key, static function ($item) {
            return $item;
        });
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function remap($from, $to)
    {
        return new static(ArrayHelper::map($this->all(), $from, $to));
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function groupBy($groupField, $preserveKeys = true)
    {
        $result = [];

        foreach ($this->all() as $key => $element) {
            if ($preserveKeys) {
                $result[ArrayHelper::getValue($element, $groupField)][$key] = $element;
            } else {
                $result[ArrayHelper::getValue($element, $groupField)][] = $element;
            }
        }

        return new static(array_map(static function ($groupItems) {
            return new static($groupItems);
        }, $result));
    }

    /**
     * Check whether the collection contains a specific item.
     * @param mixed|Closure $item the item to search for. You may also pass a closure that returns a boolean.
     * The closure will be called on each item and in case it returns `true`, the item will be considered to
     * be found. In case a closure is passed, `$strict` parameter has no effect.
     * @param bool $strict whether comparison should be compared strict (`===`) or not (`==`).
     * Defaults to `false`.
     * @return bool `true` if the collection contains at least one item that matches, `false` if not.
     */
    public function contains($item, $strict = false): bool
    {
        if ($item instanceof Closure) {
            $test = $item;
        } else {
            $test = static function ($i) use ($strict, $item) {
                /** @noinspection TypeUnsafeComparisonInspection */
                return $strict ? $i === $item : $i == $item;
            };
        }

        foreach ($this->all() as $i) {
            if ($test($i)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove a specific item from the collection.
     *
     * The original collection will not be changed, a new collection with modified data is returned.
     * @param mixed|Closure $item the item to search for. You may also pass a closure that returns a boolean.
     * The closure will be called on each item and in case it returns `true`, the item will be removed.
     * In case a closure is passed, `$strict` parameter has no effect.
     * @param bool $strict whether comparison should be compared strict (`===`) or not (`==`).
     * Defaults to `false`.
     * @return static a new collection containing the filtered items.
     * @see filter()
     */
    public function remove($item, $strict = false)
    {
        if ($item instanceof Closure) {
            $fun = static function ($i) use ($item) {
                return !$item($i);
            };
        } elseif ($strict) {
            $fun = static function ($i) use ($item) {
                return $i !== $item;
            };
        } else {
            $fun = static function ($i) use ($item) {
                /** @noinspection TypeUnsafeComparisonInspection */
                return $i != $item;
            };
        }
        return $this->filter($fun);
    }

    /**
     * {@inheritDoc}
     *
     * The original collection will not be changed, a new collection with modified data is returned.
     *
     * @return static a new collection containing the filtered items.
     */
    public function filter(callable $callback = null)
    {
        return new static(array_filter($this->all(), $callback, ARRAY_FILTER_USE_BOTH));
    }

    /** {@inheritDoc} */
    public function find(callable $callback)
    {
        foreach ($this->all() as $key => $item) {
            if ($callback($item, $key) === true) {
                return $item;
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     * @param bool $strict whether comparison should be compared strict (`===`) or not (`==`).
     * Defaults to `false`.
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function replace($item, $replacement, $strict = false)
    {
        return $this->map(static function ($i) use ($item, $replacement, $strict) {
            /** @noinspection TypeUnsafeComparisonInspection */
            if ($strict ? $i === $item : $i == $item) {
                return $replacement;
            }
            return $i;
        });
    }

    /** {@inheritDoc} */
    public function map(callable $callback)
    {
        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);
        return new static(array_combine($keys, $items));
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function slice($offset, $limit = null, $preserveKeys = true)
    {
        return new static(array_slice($this->all(), $offset, $limit, $preserveKeys));
    }

    /** {@inheritDoc} */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /** {@inheritDoc} */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    /** {@inheritDoc} */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /** {@inheritDoc} */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /** {@inheritDoc} */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * Convert the collection to its string representation.
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Get the collection of items as JSON.
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /** {@inheritDoc} */
    public function jsonSerialize()
    {
        return $this->map(static function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            }

            return $value;
        })->all();
    }
}
