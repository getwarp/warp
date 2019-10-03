<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use ArrayAccess;
use Closure;
use Countable;
use IteratorAggregate;

interface CollectionInterface extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Get all of the items in the collection.
     * @return array
     */
    public function all(): array;

    /**
     * Execute a callback over each item.
     * @param callable $callback
     * @return CollectionInterface
     */
    public function each(callable $callback);

    /**
     * Filter items from the collection.
     * @param callable|null $callable the callback function to decide which items to remove.
     * @return CollectionInterface
     */
    public function filter(callable $callable = null);

    /**
     * Reduce the collection to a single value.
     * @param callable $callback the callback function to compute the reduce value.
     * @param mixed $initialValue initial value to pass to the callback on first item.
     * @return mixed
     */
    public function reduce(callable $callback, $initialValue = null);

    /**
     * Calculate the sum of a field of the models in the collection.
     * @param mixed $field the name of the field to calculate.
     * @return int|float the calculated sum.
     */
    public function sum($field = null);

    /**
     * Calculate the maximum value of a field of the models in the collection.
     * @param mixed $field the name of the field to calculate.
     * @return int|float|null the calculated maximum value. `null` if the collection is empty.
     */
    public function max($field = null);

    /**
     * Calculate the minimum value of a field of the models in the collection
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
    public function map(callable $callback);

    /**
     * Sort collection data by value.
     *
     * If the collection values are not scalar types, use `sortBy()` instead.
     *
     * @param int $direction sort direction, either `SORT_ASC` or `SORT_DESC`.
     * @param int $sortFlag type of comparison, either `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`,
     * `SORT_LOCALE_STRING`, `SORT_NATURAL` or `SORT_FLAG_CASE`.
     * See [the PHP manual](http://php.net/manual/en/function.sort.php#refsect1-function.sort-parameters)
     * for details.
     * @return CollectionInterface
     */
    public function sort($direction = SORT_ASC, $sortFlag = SORT_REGULAR);

    /**
     * Sort collection data by key.
     * @param int $direction sort direction, either `SORT_ASC` or `SORT_DESC`.
     * @param int $sortFlag type of comparison, either `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`,
     * `SORT_LOCALE_STRING`, `SORT_NATURAL` or `SORT_FLAG_CASE`.
     * See [the PHP manual](http://php.net/manual/en/function.sort.php#refsect1-function.sort-parameters)
     * for details.
     * @return CollectionInterface
     */
    public function sortByKey($direction = SORT_ASC, $sortFlag = SORT_REGULAR);

    /**
     * Sort collection data by value using natural sort comparison.
     *
     * If the collection values are not scalar types, use `sortBy()` instead.
     *
     * @param bool $caseSensitive whether comparison should be done in a case-sensitive manner. Defaults to `false`.
     * @return CollectionInterface
     */
    public function sortNatural($caseSensitive = false);

    /**
     * Sort collection data by one or multiple values.
     * @param string|Closure|array $key the key(s) to be sorted by. This refers to a key name of the sub-array
     * elements, a property name of the objects, or an anonymous function returning the values for comparison
     * purpose. The anonymous function signature should be: `function($item)`.
     * To sort by multiple keys, provide an array of keys here.
     * @param int|array $direction the sorting direction. It can be either `SORT_ASC` or `SORT_DESC`.
     * When sorting by multiple keys with different sorting directions, use an array of sorting directions.
     * @param int|array $sortFlag the PHP sort flag. Valid values include
     * `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, `SORT_LOCALE_STRING`, `SORT_NATURAL` and `SORT_FLAG_CASE`.
     * Please refer to the [PHP manual](http://php.net/manual/en/function.sort.php)
     * for more details. When sorting by multiple keys with different sort flags, use an array of sort flags.
     * @return static a new collection containing the sorted items.
     */
    public function sortBy($key, $direction = SORT_ASC, $sortFlag = SORT_REGULAR);

    /**
     * Reverse the order of items.
     * @return CollectionInterface
     */
    public function reverse();

    /**
     * Return items without keys.
     * @return CollectionInterface
     */
    public function values();

    /**
     * Return keys of all collection items.
     * @return CollectionInterface
     */
    public function keys();

    /**
     * Flip keys and values of all collection items.
     * @return CollectionInterface
     */
    public function flip();

    /**
     * Merge one or more arrays or collections with current collection.
     * @return CollectionInterface
     */
    public function merge();

    /**
     * Convert collection data by selecting a new key and a new value for each item.
     *
     * Builds a map (key-value pairs) from a multidimensional array or an array of objects.
     * The `$from` and `$to` parameters specify the key names or property names to set up the map.
     *
     * @param string|Closure $from the field of the item to use as the key of the created map.
     * This can be a closure that returns such a value.
     * @param string|Closure $to the field of the item to use as the value of the created map.
     * This can be a closure that returns such a value.
     * @return CollectionInterface
     */
    public function remap($from, $to);

    /**
     * Assign a new key to each item in the collection.
     * @param string|Closure $key the field of the item to use as the new key.
     * This can be a closure that returns such a value.
     * @return CollectionInterface
     */
    public function indexBy($key);

    /**
     * Group items by a specified field.
     * @param string|Closure $groupField the field of the item to use as the group value.
     * This can be a closure that returns such a value.
     * @param bool $preserveKeys whether to preserve item keys in the groups. Defaults to `true`.
     * @return CollectionInterface
     */
    public function groupBy($groupField, $preserveKeys = true);

    /**
     * Check whether the collection contains a specific item.
     * @param mixed|Closure $item the item to search for. You may also pass a closure that returns a boolean.
     * The closure will be called on each item and in case it returns `true`, the item will be considered to
     * be found.
     * @return bool `true` if the collection contains at least one item that matches, `false` if not.
     */
    public function contains($item): bool;

    /**
     * Remove a specific item from the collection.
     * @param mixed|Closure $item the item to search for. You may also pass a closure that returns a boolean.
     * The closure will be called on each item and in case it returns `true`, the item will be removed.
     * @return CollectionInterface
     * @see filter()
     */
    public function remove($item);

    /**
     * Replace a specific item in the collection with another one.
     * @param mixed $item the item to search for.
     * @param mixed $replacement the replacement to insert instead of the item.
     * @return CollectionInterface
     * @see map()
     */
    public function replace($item, $replacement);

    /**
     * Slice the set of elements by an offset and number of items to return.
     * @param int $offset starting offset for the slice.
     * @param int|null $limit the number of elements to return at maximum.
     * @param bool $preserveKeys whether to preserve item keys.
     * @return CollectionInterface
     */
    public function slice($offset, $limit = null, $preserveKeys = true);
}
