<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\Criteria\FilterableInterface;
use Traversable;

/**
 * CollectionInterface.
 *
 * @method string join(string|null $glue = null, $field = null) alias to implode()
 * @method int|float avg($field = null) alias to average()
 */
interface CollectionInterface extends ArrayAccess, Countable, IteratorAggregate, JsonSerializable, FilterableInterface
{
    public const ALIASES = [
        'join' => 'implode',
        'avg' => 'average',
    ];

    /**
     * Get all items from the collection as array.
     * @return array
     */
    public function all(): array;

    /**
     * Clear collection
     * @return $this
     */
    public function clear(): self;

    /**
     * Execute a callback over each item.
     * @param callable $callback
     * @return $this|self
     */
    public function each(callable $callback): self;

    /**
     * Filter items from the collection.
     * @param callable|null $callback the callback function to decide which items to remove.
     * @return CollectionInterface a new collection containing the filtered items.
     */
    public function filter(?callable $callback = null): self;

    /**
     * Find item in the collection
     * @param callable $callback Testing function
     * @return mixed|null First element that satisfies provided `$callback` or `null`
     */
    public function find(callable $callback);

    /**
     * Reduce the collection to a single value.
     * @param callable $callback the callback function to compute the reduce value.
     * @param mixed $initialValue initial value to pass to the callback on first item.
     * @return mixed
     */
    public function reduce(callable $callback, $initialValue = null);

    /**
     * Calculate the sum of items in the collection.
     * @param mixed $field the name of the field to calculate.
     * @return int|float the calculated sum.
     */
    public function sum($field = null);

    /**
     * Calculate the average value of items in the collection
     * @param mixed $field the name of the field to calculate.
     * @return int|float|null the calculated average value. `null` if the collection is empty.
     */
    public function average($field = null);

    /**
     * Calculate the median value of items in the collection
     * @param mixed $field the name of the field to calculate.
     * @return int|float|null the calculated median value. `null` if the collection is empty.
     */
    public function median($field = null);

    /**
     * Calculate the maximum value of items in the collection.
     * @param mixed $field the name of the field to calculate.
     * @return int|float|null the calculated maximum value. `null` if the collection is empty.
     */
    public function max($field = null);

    /**
     * Calculate the minimum value of items in the collection
     * @param mixed $field the name of the field to calculate.
     * @return int|float|null the calculated minimum value. `null` if the collection is empty.
     */
    public function min($field = null);

    /**
     * Determine if the collection is empty or not.
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Run a map over each of the items
     * @param callable $callback
     * @return CollectionInterface
     */
    public function map(callable $callback): self;

    /**
     * Sort collection data by value.
     *
     * If the collection values are not scalar types, use `sortBy()` instead.
     *
     * @param int $direction sort direction, either `SORT_ASC` or `SORT_DESC`.
     * @param int $sortFlag type of comparison, either `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`,
     *     `SORT_LOCALE_STRING`, `SORT_NATURAL` or `SORT_FLAG_CASE`. For details see
     *     [the PHP manual](http://php.net/manual/en/function.sort.php#refsect1-function.sort-parameters).
     * @return CollectionInterface
     */
    public function sort(int $direction = SORT_ASC, int $sortFlag = SORT_REGULAR): self;

    /**
     * Sort collection data by key.
     * @param int $direction sort direction, either `SORT_ASC` or `SORT_DESC`.
     * @param int $sortFlag type of comparison, either `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`,
     *     `SORT_LOCALE_STRING`, `SORT_NATURAL` or `SORT_FLAG_CASE`. For details see
     *     [the PHP manual](http://php.net/manual/en/function.sort.php#refsect1-function.sort-parameters).
     * @return CollectionInterface
     */
    public function sortByKey(int $direction = SORT_ASC, int $sortFlag = SORT_REGULAR): self;

    /**
     * Sort collection data by value using natural sort comparison.
     *
     * If the collection values are not scalar types, use `sortBy()` instead.
     *
     * @param bool $caseSensitive whether comparison should be done in a case-sensitive manner. Defaults to `false`.
     * @return CollectionInterface
     */
    public function sortNatural(bool $caseSensitive = false): self;

    /**
     * Sort collection data by one or multiple values.
     * @param string|callable|array $key the key(s) to be sorted by. This refers to a key name of the sub-array
     *     elements, a property name of the objects, or an anonymous function returning the values for comparison
     *     purpose. The anonymous function signature should be: `function($item)`. To sort by multiple keys, provide an
     *     array of keys here.
     * @param int|array $direction the sorting direction. It can be either `SORT_ASC` or `SORT_DESC`. When sorting by
     *     multiple keys with different sorting directions, use an array of sorting directions.
     * @param int|array $sortFlag the PHP sort flag. Valid values include `SORT_REGULAR`, `SORT_NUMERIC`,
     *     `SORT_STRING`, `SORT_LOCALE_STRING`, `SORT_NATURAL` and `SORT_FLAG_CASE`. Please refer to
     *     [the PHP manual](http://php.net/manual/en/function.sort.php) for more details.
     *     When sorting by multiple keys with different sort flags, use an array of sort flags.
     * @return CollectionInterface a new collection containing the sorted items.
     */
    public function sortBy($key, $direction = SORT_ASC, $sortFlag = SORT_REGULAR): self;

    /**
     * Reverse the order of items.
     * @return CollectionInterface
     */
    public function reverse(): self;

    /**
     * Return items without keys.
     * @return CollectionInterface
     */
    public function values(): self;

    /**
     * Return keys of all collection items.
     * @return CollectionInterface
     */
    public function keys(): self;

    /**
     * Flip keys and values of all collection items.
     * @return CollectionInterface
     */
    public function flip(): self;

    /**
     * Merge one or more arrays or collections with current collection.
     * @param iterable[] $collections
     * @return CollectionInterface
     */
    public function merge(...$collections): self;

    /**
     * Convert collection data by selecting a new key and a new value for each item.
     *
     * Builds a map (key-value pairs) from a multidimensional array or an array of objects.
     * The `$from` and `$to` parameters specify the key names or property names to set up the map.
     *
     * @param string|callable $from the field of the item to use as the key of the created map. This can be a callable
     *     that returns such a value.
     * @param string|callable $to the field of the item to use as the value of the created map. This can be a callable
     *     that returns such a value.
     * @return CollectionInterface
     */
    public function remap($from, $to): self;

    /**
     * Assign a new key to each item in the collection.
     * @param string|callable $key the field of the item to use as the new key. This can be a callable that returns
     *     such a value.
     * @return CollectionInterface
     */
    public function indexBy($key): self;

    /**
     * Group items by a specified field.
     * @param string|callable $groupField the field of the item to use as the group value. This can be a callable that
     *     returns such a value.
     * @param bool $preserveKeys whether to preserve item keys in the groups. Defaults to `true`.
     * @return CollectionInterface
     */
    public function groupBy($groupField, bool $preserveKeys = true): self;

    /**
     * Check whether the collection contains a specific item.
     * @param mixed|callable $item the item to search for. You may also pass a callable that returns a boolean. The
     *     callable will be called on each item and in case it returns `true`, the item will be considered to be found.
     * @param bool $strict whether comparison should be compared strict (`===`) or not (`==`). Defaults to `false`.
     * @return bool `true` if the collection contains at least one item that matches, `false` if not.
     */
    public function contains($item, bool $strict = false): bool;

    /**
     * Remove a specific item from the collection.
     * @param mixed|callable $item the item to search for. You may also pass a callable that returns a boolean. The
     *     callable will be called on each item and in case it returns `true`, the item will be removed.
     * @param bool $strict whether comparison should be compared strict (`===`) or not (`==`). Defaults to `false`.
     * @return CollectionInterface
     * @see filter()
     */
    public function remove($item, bool $strict = false): self;

    /**
     * Replace a specific item in the collection with another one.
     * @param mixed $item the item to search for.
     * @param mixed $replacement the replacement to insert instead of the item.
     * @param bool $strict whether comparison should be compared strict (`===`) or not (`==`). Defaults to `false`.
     * @return CollectionInterface
     * @see map()
     */
    public function replace($item, $replacement, bool $strict = false): self;

    /**
     * Slice the set of elements by an offset and number of items to return.
     * @param int $offset starting offset for the slice.
     * @param int|null $limit the number of elements to return at maximum.
     * @param bool $preserveKeys whether to preserve item keys.
     * @return CollectionInterface
     */
    public function slice(int $offset, ?int $limit = null, bool $preserveKeys = true): self;

    /**
     * Filter collection matching given criteria.
     * @param CriteriaInterface $criteria
     * @return CollectionInterface
     */
    public function matching(CriteriaInterface $criteria): self;

    /**
     * Removes duplicate values from the collection
     * @param int $sortFlags sort flags argument for array_unique. Defaults to `SORT_REGULAR`.
     * @return CollectionInterface
     */
    public function unique(int $sortFlags = SORT_REGULAR): self;

    /**
     * Join collection elements with string
     * @param string|null $glue glue string
     * @param mixed $field the name of the field to join.
     * @return string
     */
    public function implode(?string $glue = null, $field = null): string;

    /**
     * Returns first item of the collection
     * @return mixed
     */
    public function first();

    /**
     * Returns first key of the collection
     * @return mixed
     */
    public function firstKey();

    /**
     * Returns last item of the collection
     * @return mixed
     */
    public function last();

    /**
     * Returns last key of the collection
     * @return mixed
     */
    public function lastKey();

    /**
     * Get the collection of items as JSON.
     * @param int $options
     * @return string
     */
    public function toJson(int $options = 0): string;

    /**
     * Convert the collection to its string representation.
     * @return string
     */
    public function __toString(): string;

    /**
     * Retrieve an external iterator
     * @return Traversable An instance of an object implementing `Iterator` or `Traversable`
     */
    public function getIterator(): Traversable;
}
