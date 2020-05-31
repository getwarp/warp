# Interface CollectionInterface

Interface CollectionInterface

-   Full name: `\spaceonfire\Collection\CollectionInterface`
-   This interface extends:
    -   `\ArrayAccess`
    -   `\Countable`
    -   `\IteratorAggregate`
    -   `\JsonSerializable`
    -   `\spaceonfire\Criteria\FilterableInterface`

## Constants

| Constant  | Value                                       | Description |
| --------- | ------------------------------------------- | ----------- |
| `ALIASES` | `['join' => 'implode', 'avg' => 'average']` |             |

## Methods

### \_\_toString()

Convert the collection to its string representation.

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| **Return** | _string_ |             |

```php
public function CollectionInterface::__toString(): mixed
```

File location: `src/CollectionInterface.php:324`

### all()

Get all items from the collection as array.

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _array_ |             |

```php
public function CollectionInterface::all(): array
```

File location: `src/CollectionInterface.php:33`

### average()

Calculate the average value of items in the collection

| Param      | Type             | Description                                                      |
| ---------- | ---------------- | ---------------------------------------------------------------- |
| `$field`   | _mixed_          | the name of the field to calculate.                              |
| **Return** | _int&#124;float_ | the calculated average value. `null` if the collection is empty. |

```php
public function CollectionInterface::average(mixed $field = null): mixed
```

File location: `src/CollectionInterface.php:82`

### clear()

Clear collection

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| **Return** | _\$this_ |             |

```php
public function CollectionInterface::clear(): self
```

File location: `src/CollectionInterface.php:39`

### contains()

Check whether the collection contains a specific item.

| Param                                                                                                        | Type                  | Description                                                                              |
| ------------------------------------------------------------------------------------------------------------ | --------------------- | ---------------------------------------------------------------------------------------- |
| `$item`                                                                                                      | _mixed&#124;callable_ | the item to search for. You may also pass a callable that returns a boolean. The         |
| callable will be called on each item and in case it returns `true`, the item will be considered to be found. |
| `$strict`                                                                                                    | _bool_                | whether comparison should be compared strict (`===`) or not (`==`). Defaults to `false`. |
| **Return**                                                                                                   | _bool_                | `true` if the collection contains at least one item that matches, `false` if not.        |

```php
public function CollectionInterface::contains(mixed $item, mixed $strict = false): bool
```

File location: `src/CollectionInterface.php:236`

### each()

Execute a callback over each item.

| Param       | Type                                          | Description |
| ----------- | --------------------------------------------- | ----------- |
| `$callback` | _callable_                                    |             |
| **Return**  | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function CollectionInterface::each(callable $callback): mixed
```

File location: `src/CollectionInterface.php:46`

### filter()

Filter items from the collection.

| Param       | Type                                          | Description                                            |
| ----------- | --------------------------------------------- | ------------------------------------------------------ |
| `$callback` | _callable&#124;null_                          | the callback function to decide which items to remove. |
| **Return**  | _\spaceonfire\Collection\CollectionInterface_ | a new collection containing the filtered items.        |

```php
public function CollectionInterface::filter(?callable $callback = null): mixed
```

File location: `src/CollectionInterface.php:53`

### find()

Find item in the collection

| Param       | Type              | Description                                                 |
| ----------- | ----------------- | ----------------------------------------------------------- |
| `$callback` | _callable_        | Testing function                                            |
| **Return**  | _mixed&#124;null_ | First element that satisfies provided `$callback` or `null` |

```php
public function CollectionInterface::find(callable $callback): mixed
```

File location: `src/CollectionInterface.php:60`

### first()

Returns first item of the collection

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _mixed_ |             |

```php
public function CollectionInterface::first(): mixed
```

File location: `src/CollectionInterface.php:293`

### firstKey()

Returns first key of the collection

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _mixed_ |             |

```php
public function CollectionInterface::firstKey(): mixed
```

File location: `src/CollectionInterface.php:299`

### flip()

Flip keys and values of all collection items.

| Param      | Type                                          | Description |
| ---------- | --------------------------------------------- | ----------- |
| **Return** | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function CollectionInterface::flip(): mixed
```

File location: `src/CollectionInterface.php:189`

### getIterator()

Retrieve an external iterator

| Param      | Type           | Description                                                       |
| ---------- | -------------- | ----------------------------------------------------------------- |
| **Return** | _\Traversable_ | An instance of an object implementing `Iterator` or `Traversable` |

```php
public function CollectionInterface::getIterator(): mixed
```

File location: `src/CollectionInterface.php:330`

### groupBy()

Group items by a specified field.

| Param                 | Type                                          | Description                                                                  |
| --------------------- | --------------------------------------------- | ---------------------------------------------------------------------------- |
| `$groupField`         | _string&#124;callable_                        | the field of the item to use as the group value. This can be a callable that |
| returns such a value. |
| `$preserveKeys`       | _bool_                                        | whether to preserve item keys in the groups. Defaults to `true`.             |
| **Return**            | _\spaceonfire\Collection\CollectionInterface_ |                                                                              |

```php
public function CollectionInterface::groupBy(mixed $groupField, mixed $preserveKeys = true): mixed
```

File location: `src/CollectionInterface.php:227`

### implode()

Join collection elements with string

| Param      | Type               | Description                    |
| ---------- | ------------------ | ------------------------------ |
| `$glue`    | _string&#124;null_ | glue string                    |
| `$field`   | _mixed_            | the name of the field to join. |
| **Return** | _string_           |                                |

```php
public function CollectionInterface::implode(?string $glue = null, mixed $field = null): string
```

File location: `src/CollectionInterface.php:287`

### indexBy()

Assign a new key to each item in the collection.

| Param         | Type                                          | Description                                                                      |
| ------------- | --------------------------------------------- | -------------------------------------------------------------------------------- |
| `$key`        | _string&#124;callable_                        | the field of the item to use as the new key. This can be a callable that returns |
| such a value. |
| **Return**    | _\spaceonfire\Collection\CollectionInterface_ |                                                                                  |

```php
public function CollectionInterface::indexBy(mixed $key): mixed
```

File location: `src/CollectionInterface.php:218`

### isEmpty()

Determine if the collection is empty or not.

| Param      | Type   | Description |
| ---------- | ------ | ----------- |
| **Return** | _bool_ |             |

```php
public function CollectionInterface::isEmpty(): bool
```

File location: `src/CollectionInterface.php:109`

### keys()

Return keys of all collection items.

| Param      | Type                                          | Description |
| ---------- | --------------------------------------------- | ----------- |
| **Return** | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function CollectionInterface::keys(): mixed
```

File location: `src/CollectionInterface.php:183`

### last()

Returns last item of the collection

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _mixed_ |             |

```php
public function CollectionInterface::last(): mixed
```

File location: `src/CollectionInterface.php:305`

### lastKey()

Returns last key of the collection

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _mixed_ |             |

```php
public function CollectionInterface::lastKey(): mixed
```

File location: `src/CollectionInterface.php:311`

### map()

Run a map over each of the items

| Param       | Type                                          | Description |
| ----------- | --------------------------------------------- | ----------- |
| `$callback` | _callable_                                    |             |
| **Return**  | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function CollectionInterface::map(callable $callback): mixed
```

File location: `src/CollectionInterface.php:116`

### matching()

Filter collection matching given criteria.

| Param       | Type                                      | Description |
| ----------- | ----------------------------------------- | ----------- |
| `$criteria` | _\spaceonfire\Criteria\CriteriaInterface_ |             |
| **Return**  | _self_                                    |             |

```php
public function CollectionInterface::matching(\spaceonfire\Criteria\CriteriaInterface $criteria): self
```

File location: `src/CollectionInterface.php:272`

### max()

Calculate the maximum value of items in the collection.

| Param      | Type                       | Description                                                      |
| ---------- | -------------------------- | ---------------------------------------------------------------- |
| `$field`   | _mixed_                    | the name of the field to calculate.                              |
| **Return** | _int&#124;float&#124;null_ | the calculated maximum value. `null` if the collection is empty. |

```php
public function CollectionInterface::max(mixed $field = null): mixed
```

File location: `src/CollectionInterface.php:96`

### median()

Calculate the median value of items in the collection

| Param      | Type                       | Description                                                     |
| ---------- | -------------------------- | --------------------------------------------------------------- |
| `$field`   | _mixed_                    | the name of the field to calculate.                             |
| **Return** | _int&#124;float&#124;null_ | the calculated median value. `null` if the collection is empty. |

```php
public function CollectionInterface::median(mixed $field = null): mixed
```

File location: `src/CollectionInterface.php:89`

### merge()

Merge one or more arrays or collections with current collection.

| Param          | Type                                          | Description |
| -------------- | --------------------------------------------- | ----------- |
| `$collections` | _iterable[]_                                  |             |
| **Return**     | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function CollectionInterface::merge(mixed ...$collections): mixed
```

File location: `src/CollectionInterface.php:196`

### min()

Calculate the minimum value of items in the collection

| Param      | Type                       | Description                                                      |
| ---------- | -------------------------- | ---------------------------------------------------------------- |
| `$field`   | _mixed_                    | the name of the field to calculate.                              |
| **Return** | _int&#124;float&#124;null_ | the calculated minimum value. `null` if the collection is empty. |

```php
public function CollectionInterface::min(mixed $field = null): mixed
```

File location: `src/CollectionInterface.php:103`

### reduce()

Reduce the collection to a single value.

| Param           | Type       | Description                                          |
| --------------- | ---------- | ---------------------------------------------------- |
| `$callback`     | _callable_ | the callback function to compute the reduce value.   |
| `$initialValue` | _mixed_    | initial value to pass to the callback on first item. |
| **Return**      | _mixed_    |                                                      |

```php
public function CollectionInterface::reduce(callable $callback, mixed $initialValue = null): mixed
```

File location: `src/CollectionInterface.php:68`

### remap()

Convert collection data by selecting a new key and a new value for each item.

Builds a map (key-value pairs) from a multidimensional array or an array of objects.
The `$from` and `$to` parameters specify the key names or property names to set up the map.

| Param                      | Type                                          | Description                                                                          |
| -------------------------- | --------------------------------------------- | ------------------------------------------------------------------------------------ |
| `$from`                    | _string&#124;callable_                        | the field of the item to use as the key of the created map. This can be a callable   |
| that returns such a value. |
| `$to`                      | _string&#124;callable_                        | the field of the item to use as the value of the created map. This can be a callable |
| that returns such a value. |
| **Return**                 | _\spaceonfire\Collection\CollectionInterface_ |                                                                                      |

```php
public function CollectionInterface::remap(mixed $from, mixed $to): mixed
```

File location: `src/CollectionInterface.php:210`

### remove()

Remove a specific item from the collection.

| Param                                                                                         | Type                                          | Description                                                                              |
| --------------------------------------------------------------------------------------------- | --------------------------------------------- | ---------------------------------------------------------------------------------------- |
| `$item`                                                                                       | _mixed&#124;callable_                         | the item to search for. You may also pass a callable that returns a boolean. The         |
| callable will be called on each item and in case it returns `true`, the item will be removed. |
| `$strict`                                                                                     | _bool_                                        | whether comparison should be compared strict (`===`) or not (`==`). Defaults to `false`. |
| **Return**                                                                                    | _\spaceonfire\Collection\CollectionInterface_ |                                                                                          |

```php
public function CollectionInterface::remove(mixed $item, mixed $strict = false): mixed
```

File location: `src/CollectionInterface.php:246`

### replace()

Replace a specific item in the collection with another one.

| Param          | Type                                          | Description                                                                              |
| -------------- | --------------------------------------------- | ---------------------------------------------------------------------------------------- |
| `$item`        | _mixed_                                       | the item to search for.                                                                  |
| `$replacement` | _mixed_                                       | the replacement to insert instead of the item.                                           |
| `$strict`      | _bool_                                        | whether comparison should be compared strict (`===`) or not (`==`). Defaults to `false`. |
| **Return**     | _\spaceonfire\Collection\CollectionInterface_ |                                                                                          |

```php
public function CollectionInterface::replace(mixed $item, mixed $replacement, mixed $strict = false): mixed
```

File location: `src/CollectionInterface.php:256`

### reverse()

Reverse the order of items.

| Param      | Type                                          | Description |
| ---------- | --------------------------------------------- | ----------- |
| **Return** | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function CollectionInterface::reverse(): mixed
```

File location: `src/CollectionInterface.php:171`

### slice()

Slice the set of elements by an offset and number of items to return.

| Param           | Type                                          | Description                                  |
| --------------- | --------------------------------------------- | -------------------------------------------- |
| `$offset`       | _int_                                         | starting offset for the slice.               |
| `$limit`        | _int&#124;null_                               | the number of elements to return at maximum. |
| `$preserveKeys` | _bool_                                        | whether to preserve item keys.               |
| **Return**      | _\spaceonfire\Collection\CollectionInterface_ |                                              |

```php
public function CollectionInterface::slice(mixed $offset, mixed $limit = null, mixed $preserveKeys = true): mixed
```

File location: `src/CollectionInterface.php:265`

### sort()

Sort collection data by value.

If the collection values are not scalar types, use `sortBy()` instead.

| Param        | Type  | Description                                                               |
| ------------ | ----- | ------------------------------------------------------------------------- |
| `$direction` | _int_ | sort direction, either `SORT_ASC` or `SORT_DESC`.                         |
| `$sortFlag`  | _int_ | type of comparison, either `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, |

`SORT_LOCALE_STRING`, `SORT_NATURAL` or `SORT_FLAG_CASE`. For details see
[the PHP manual](http://php.net/manual/en/function.sort.php#refsect1-function.sort-parameters).|
|**Return**|_\spaceonfire\Collection\CollectionInterface_||

```php
public function CollectionInterface::sort(mixed $direction = SORT_ASC, mixed $sortFlag = SORT_REGULAR): mixed
```

File location: `src/CollectionInterface.php:129`

### sortBy()

Sort collection data by one or multiple values.

| Param  | Type                              | Description                                                            |
| ------ | --------------------------------- | ---------------------------------------------------------------------- |
| `$key` | _string&#124;callable&#124;array_ | the key(s) to be sorted by. This refers to a key name of the sub-array |

elements, a property name of the objects, or an anonymous function returning the values for comparison
purpose. The anonymous function signature should be: `function($item)`. To sort by multiple keys, provide an
array of keys here.|
|`$direction`|_int&#124;array_|the sorting direction. It can be either `SORT_ASC` or `SORT_DESC`. When sorting by
multiple keys with different sorting directions, use an array of sorting directions.|
|`$sortFlag`|_int&#124;array_|the PHP sort flag. Valid values include `SORT_REGULAR`, `SORT_NUMERIC`,
`SORT_STRING`, `SORT_LOCALE_STRING`, `SORT_NATURAL` and `SORT_FLAG_CASE`. Please refer to
[the PHP manual](http://php.net/manual/en/function.sort.php) for more details.
When sorting by multiple keys with different sort flags, use an array of sort flags.|
|**Return**|_\spaceonfire\Collection\CollectionInterface_|a new collection containing the sorted items.|

```php
public function CollectionInterface::sortBy(mixed $key, mixed $direction = SORT_ASC, mixed $sortFlag = SORT_REGULAR): mixed
```

File location: `src/CollectionInterface.php:165`

### sortByKey()

Sort collection data by key.

| Param        | Type  | Description                                                               |
| ------------ | ----- | ------------------------------------------------------------------------- |
| `$direction` | _int_ | sort direction, either `SORT_ASC` or `SORT_DESC`.                         |
| `$sortFlag`  | _int_ | type of comparison, either `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, |

`SORT_LOCALE_STRING`, `SORT_NATURAL` or `SORT_FLAG_CASE`. For details see
[the PHP manual](http://php.net/manual/en/function.sort.php#refsect1-function.sort-parameters).|
|**Return**|_\spaceonfire\Collection\CollectionInterface_||

```php
public function CollectionInterface::sortByKey(mixed $direction = SORT_ASC, mixed $sortFlag = SORT_REGULAR): mixed
```

File location: `src/CollectionInterface.php:139`

### sortNatural()

Sort collection data by value using natural sort comparison.

If the collection values are not scalar types, use `sortBy()` instead.

| Param            | Type                                          | Description                                                                        |
| ---------------- | --------------------------------------------- | ---------------------------------------------------------------------------------- |
| `$caseSensitive` | _bool_                                        | whether comparison should be done in a case-sensitive manner. Defaults to `false`. |
| **Return**       | _\spaceonfire\Collection\CollectionInterface_ |                                                                                    |

```php
public function CollectionInterface::sortNatural(mixed $caseSensitive = false): mixed
```

File location: `src/CollectionInterface.php:149`

### sum()

Calculate the sum of items in the collection.

| Param      | Type             | Description                         |
| ---------- | ---------------- | ----------------------------------- |
| `$field`   | _mixed_          | the name of the field to calculate. |
| **Return** | _int&#124;float_ | the calculated sum.                 |

```php
public function CollectionInterface::sum(mixed $field = null): mixed
```

File location: `src/CollectionInterface.php:75`

### toJson()

Get the collection of items as JSON.

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| `$options` | _int_    |             |
| **Return** | _string_ |             |

```php
public function CollectionInterface::toJson(int $options): string
```

File location: `src/CollectionInterface.php:318`

### unique()

Removes duplicate values from the collection

| Param        | Type                                          | Description                                                       |
| ------------ | --------------------------------------------- | ----------------------------------------------------------------- |
| `$sortFlags` | _int_                                         | sort flags argument for array_unique. Defaults to `SORT_REGULAR`. |
| **Return**   | _\spaceonfire\Collection\CollectionInterface_ |                                                                   |

```php
public function CollectionInterface::unique(int $sortFlags = SORT_REGULAR): mixed
```

File location: `src/CollectionInterface.php:279`

### values()

Return items without keys.

| Param      | Type                                          | Description |
| ---------- | --------------------------------------------- | ----------- |
| **Return** | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function CollectionInterface::values(): mixed
```

File location: `src/CollectionInterface.php:177`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)
