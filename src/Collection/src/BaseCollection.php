<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use ArrayIterator;
use BadMethodCallException;
use InvalidArgumentException;
use JsonSerializable;
use RuntimeException;
use spaceonfire\Criteria\CriteriaInterface;
use Traversable;

/**
 * Abstract class `BaseCollection` contains base implementation of `CollectionInterface`.
 * Use it for building your custom collection classes.
 *
 * @package spaceonfire\Collection
 *
 * @method string join(string|null $glue = null, $field = null) alias to implode()
 * @method int|float avg($field = null) alias to average()
 */
abstract class BaseCollection implements CollectionInterface
{
    use CollectionAliasesTrait;

    /**
     * @var array The items contained in the collection.
     */
    protected $items = [];

    /**
     * BaseCollection constructor.
     * @param array|iterable|mixed $items
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

        if ($items instanceof Traversable) {
            return iterator_to_array($items);
        }

        if ($items instanceof JsonSerializable) {
            return $items->jsonSerialize();
        }

        return (array)$items;
    }

    /**
     * Creates new instance of collection
     * @param array $items
     * @return static
     */
    protected function newStatic(array $items = []): CollectionInterface
    {
        return new static($items);
    }

    /** {@inheritDoc} */
    public function all(): array
    {
        return $this->items;
    }

    /** {@inheritDoc} */
    public function clear(): CollectionInterface
    {
        $this->items = [];
        return $this;
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
     * @param string|callable|array|null $field the name of the field to calculate. This will be passed to
     *     `ArrayHelper::getValue()`.
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
    public function average($field = null)
    {
        return $this->sum($field) / $this->count();
    }

    /** {@inheritDoc} */
    public function median($field = null)
    {
        $items = [];

        foreach ($this->items as $item) {
            $value = $field === null ? $item : ArrayHelper::getValue($item, $field);

            if (!is_numeric($value)) {
                throw new BadMethodCallException('Non-numeric value used in ' . __METHOD__);
            }

            /** @noinspection TypeUnsafeComparisonInspection */
            $items[] = (int)$value == $value ? (int)$value : (float)$value;
        }

        if (empty($items)) {
            return null;
        }

        $count = count($items);
        $middleIndex = floor(($count - 1) / 2);

        sort($items, SORT_NATURAL);

        if ($count % 2) {
            return $items[$middleIndex];
        }

        return ($items[$middleIndex] + $items[$middleIndex + 1]) / 2;
    }

    /** {@inheritDoc} */
    public function reduce(callable $callback, $initialValue = null)
    {
        return array_reduce($this->all(), $callback, $initialValue);
    }

    /**
     * Calculate the maximum value of a field of the models in the collection.
     * @param string|callable|array $field the name of the field to calculate. This will be passed to
     *     `ArrayHelper::getValue()`.
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
     * @param string|callable|array $field the name of the field to calculate. This will be passed to
     *     `ArrayHelper::getValue()`.
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

    /**
     * {@inheritDoc}
     * @return int
     */
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
        return $this->newStatic($items);
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
        return $this->newStatic($items);
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
        return $this->newStatic($items);
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
     * @throws InvalidArgumentException if the $direction or $sortFlag parameters do not have correct number of
     *     elements as that of $key.
     * @see ArrayHelper::multisort()
     */
    public function sortBy($key, $direction = SORT_ASC, $sortFlag = SORT_REGULAR)
    {
        $items = $this->all();
        ArrayHelper::multisort($items, $key, $direction, $sortFlag);
        return $this->newStatic($items);
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function reverse()
    {
        return $this->newStatic(array_reverse($this->all(), true));
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function values()
    {
        return $this->newStatic(array_values($this->all()));
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function keys()
    {
        return $this->newStatic(array_keys($this->all()));
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function flip()
    {
        return $this->newStatic(array_flip($this->all()));
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
        return $this->newStatic(array_merge(
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
        return $this->newStatic(ArrayHelper::map($this->all(), $from, $to));
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

        return $this->newStatic(array_map(function ($groupItems) {
            return $this->newStatic($groupItems);
        }, $result));
    }

    /**
     * {@inheritDoc}
     */
    public function contains($item, $strict = false): bool
    {
        if (is_callable($item)) {
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
     * {@inheritDoc}
     * The original collection will not be changed, a new collection with modified data is returned.
     */
    public function remove($item, $strict = false)
    {
        if (is_callable($item)) {
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
     * The original collection will not be changed, a new collection with modified data is returned.
     */
    public function filter(?callable $callback = null)
    {
        if ($callback === null) {
            return $this->newStatic(array_filter($this->all()));
        }

        return $this->newStatic(array_filter($this->all(), $callback, ARRAY_FILTER_USE_BOTH));
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
        return $this->newStatic(array_combine($keys, $items) ?: $items);
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function slice($offset, $limit = null, $preserveKeys = true)
    {
        return $this->newStatic(array_slice($this->all(), $offset, $limit, $preserveKeys));
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function matching(CriteriaInterface $criteria): CollectionInterface
    {
        $result = $this->newStatic($this->items);

        if (null !== $expression = $criteria->getWhere()) {
            $result = $result->filter(static function ($item) use ($expression) {
                return $expression->evaluate($item);
            });
        }

        if ([] !== $orderBy = $criteria->getOrderBy()) {
            foreach ($orderBy as $key => $direction) {
                $result = $result->sortBy($key, $direction);
            }
        }

        return $result->slice($criteria->getOffset(), $criteria->getLimit());
    }

    /**
     * {@inheritDoc}
     * The original collection will not be changed, a new collection will be returned instead.
     */
    public function unique(int $sortFlags = SORT_REGULAR)
    {
        return $this->newStatic(array_unique($this->items, $sortFlags));
    }

    /** {@inheritDoc} */
    public function implode(?string $glue = null, $field = null): string
    {
        $items = [];

        foreach ($this->items as $item) {
            $value = $field === null ? $item : ArrayHelper::getValue($item, $field);

            if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                $items[] = $value;
                continue;
            }

            throw new BadMethodCallException('Value that could not be converted to string used in ' . __METHOD__);
        }

        return $glue === null ? implode($items) : implode($glue, $items);
    }

    /** {@inheritDoc} */
    public function first()
    {
        return $this->items[$this->firstKey()] ?? null;
    }

    /** {@inheritDoc} */
    public function firstKey()
    {
        return array_key_first($this->items);
    }

    /** {@inheritDoc} */
    public function last()
    {
        return $this->items[$this->lastKey()] ?? null;
    }

    /** {@inheritDoc} */
    public function lastKey()
    {
        return array_key_last($this->items);
    }

    /** {@inheritDoc} */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * {@inheritDoc}
     * @param mixed $offset
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * {@inheritDoc}
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * {@inheritDoc}
     * @param mixed|null $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * {@inheritDoc}
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * {@inheritDoc}
     */
    public function toJson(int $options = 0): string
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if ($json === false) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(
                'Error while encoding collection to JSON: ' . json_last_error_msg(),
                json_last_error()
            );
            // @codeCoverageIgnoreEnd
        }

        return $json;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * {@inheritDoc}
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->all();
    }
}
