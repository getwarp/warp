# Class TypedCollection

Class `TypedCollection` allows you to create collection witch items are the same type.

For testing scalar types provide type name from
[return values of `gettype` function](https://www.php.net/manual/en/function.gettype.php):

```php
$integers = new TypedCollection($items, 'integer');
$strings = new TypedCollection($items, 'string');
$floats = new TypedCollection($items, 'double');
```

If you want to test values are objects than provide class or interface name:

```php
$dateTime = new TypedCollection($items, \DateTime::class);
$jsonSerializable = new TypedCollection($items, \JsonSerializable::class);
```

-   Full name: `\spaceonfire\Collection\TypedCollection`
-   Parent class: `\spaceonfire\Collection\BaseCollection`
-   This class implements: `\spaceonfire\Collection\CollectionInterface`

## Methods

### \_\_call()

Magic methods call

| Param        | Type     | Description |
| ------------ | -------- | ----------- |
| `$name`      | _string_ |             |
| `$arguments` | _array_  |             |
| **Return**   | _mixed_  |             |

```php
public function CollectionAliasesTrait::__call(string $name, array $arguments = []): mixed
```

File location: `src/BaseCollection.php:17`

### \_\_construct()

TypedCollection constructor.

| Param    | Type                                 | Description                                             |
| -------- | ------------------------------------ | ------------------------------------------------------- |
| `$items` | _array_                              |                                                         |
| `$type`  | _string&#124;\spaceonfire\Type\Type_ | Scalar type name or Full qualified name of object class |

```php
public function TypedCollection::__construct(mixed $items = [], mixed $type = stdClass::class): mixed
```

File location: `src/TypedCollection.php:46`

### \_\_toString()

Convert the collection to its string representation.

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| **Return** | _string_ |             |

```php
public function BaseCollection::__toString(): mixed
```

File location: `src/BaseCollection.php:644`

### all()

Get all items from the collection as array.

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _array_ |             |

```php
public function BaseCollection::all(): array
```

File location: `src/BaseCollection.php:79`

### average()

Calculate the average value of items in the collection

| Param      | Type             | Description                                                      |
| ---------- | ---------------- | ---------------------------------------------------------------- |
| `$field`   | _mixed_          | the name of the field to calculate.                              |
| **Return** | _int&#124;float_ | the calculated average value. `null` if the collection is empty. |

```php
public function BaseCollection::average(mixed $field = null): mixed
```

File location: `src/BaseCollection.php:123`

### clear()

Clear collection

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| **Return** | _\$this_ |             |

```php
public function BaseCollection::clear(): \spaceonfire\Collection\CollectionInterface
```

File location: `src/BaseCollection.php:85`

### contains()

Check whether the collection contains a specific item.

| Param                                                                                                        | Type                  | Description                                                                              |
| ------------------------------------------------------------------------------------------------------------ | --------------------- | ---------------------------------------------------------------------------------------- |
| `$item`                                                                                                      | _mixed&#124;callable_ | the item to search for. You may also pass a callable that returns a boolean. The         |
| callable will be called on each item and in case it returns `true`, the item will be considered to be found. |
| `$strict`                                                                                                    | _bool_                | whether comparison should be compared strict (`===`) or not (`==`). Defaults to `false`. |
| **Return**                                                                                                   | _bool_                | `true` if the collection contains at least one item that matches, `false` if not.        |

```php
public function BaseCollection::contains(mixed $item, mixed $strict = false): bool
```

File location: `src/BaseCollection.php:399`

### count()

| Param      | Type  | Description |
| ---------- | ----- | ----------- |
| **Return** | _int_ |             |

```php
public function BaseCollection::count(): mixed
```

File location: `src/BaseCollection.php:226`

### downgrade()

Converts current collection to lower level collection without type check

| Param      | Type                                          | Description |
| ---------- | --------------------------------------------- | ----------- |
| **Return** | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function TypedCollection::downgrade(): \spaceonfire\Collection\CollectionInterface
```

File location: `src/TypedCollection.php:109`

### each()

Execute a callback over each item.

| Param       | Type                                          | Description |
| ----------- | --------------------------------------------- | ----------- |
| `$callback` | _callable_                                    |             |
| **Return**  | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function BaseCollection::each(callable $callback): mixed
```

File location: `src/BaseCollection.php:92`

### filter()

Filter items from the collection.

The original collection will not be changed, a new collection with modified data is returned.

| Param       | Type                                          | Description                                            |
| ----------- | --------------------------------------------- | ------------------------------------------------------ |
| `$callback` | _callable&#124;null_                          | the callback function to decide which items to remove. |
| **Return**  | _\spaceonfire\Collection\CollectionInterface_ | a new collection containing the filtered items.        |

```php
public function BaseCollection::filter(?callable $callback = null): mixed
```

File location: `src/BaseCollection.php:446`

### find()

Find item in the collection

| Param       | Type              | Description                                                 |
| ----------- | ----------------- | ----------------------------------------------------------- |
| `$callback` | _callable_        | Testing function                                            |
| **Return**  | _mixed&#124;null_ | First element that satisfies provided `$callback` or `null` |

```php
public function BaseCollection::find(callable $callback): mixed
```

File location: `src/BaseCollection.php:456`

### first()

Returns first item of the collection

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _mixed_ |             |

```php
public function BaseCollection::first(): mixed
```

File location: `src/BaseCollection.php:551`

### firstKey()

Returns first key of the collection

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _mixed_ |             |

```php
public function BaseCollection::firstKey(): mixed
```

File location: `src/BaseCollection.php:557`

### flip()

Flip keys and values of all collection items.

The original collection will not be changed, a new collection will be returned instead.

| Param      | Type                                          | Description |
| ---------- | --------------------------------------------- | ----------- |
| **Return** | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function TypedCollection::flip(): mixed
```

File location: `src/TypedCollection.php:121`

### getIterator()

Retrieve an external iterator

| Param      | Type           | Description                                                       |
| ---------- | -------------- | ----------------------------------------------------------------- |
| **Return** | _\Traversable_ | An instance of an object implementing `Iterator` or `Traversable` |

```php
public function BaseCollection::getIterator(): mixed
```

File location: `src/BaseCollection.php:575`

### groupBy()

Group items by a specified field.

The original collection will not be changed, a new collection will be returned instead.

| Param                 | Type                                          | Description                                                                  |
| --------------------- | --------------------------------------------- | ---------------------------------------------------------------------------- |
| `$groupField`         | _string&#124;callable_                        | the field of the item to use as the group value. This can be a callable that |
| returns such a value. |
| `$preserveKeys`       | _bool_                                        | whether to preserve item keys in the groups. Defaults to `true`.             |
| **Return**            | _\spaceonfire\Collection\CollectionInterface_ |                                                                              |

```php
public function TypedCollection::groupBy(mixed $groupField, mixed $preserveKeys = true): mixed
```

File location: `src/TypedCollection.php:142`

### implode()

Join collection elements with string

| Param      | Type               | Description                    |
| ---------- | ------------------ | ------------------------------ |
| `$glue`    | _string&#124;null_ | glue string                    |
| `$field`   | _mixed_            | the name of the field to join. |
| **Return** | _string_           |                                |

```php
public function BaseCollection::implode(?string $glue = null, mixed $field = null): string
```

File location: `src/BaseCollection.php:532`

### indexBy()

Assign a new key to each item in the collection.

The original collection will not be changed, a new collection will be returned instead.

| Param         | Type                                          | Description                                                                      |
| ------------- | --------------------------------------------- | -------------------------------------------------------------------------------- |
| `$key`        | _string&#124;callable_                        | the field of the item to use as the new key. This can be a callable that returns |
| such a value. |
| **Return**    | _\spaceonfire\Collection\CollectionInterface_ |                                                                                  |

```php
public function TypedCollection::indexBy(mixed $key): mixed
```

File location: `src/TypedCollection.php:136`

### isEmpty()

Determine if the collection is empty or not.

| Param      | Type   | Description |
| ---------- | ------ | ----------- |
| **Return** | _bool_ |             |

```php
public function BaseCollection::isEmpty(): bool
```

File location: `src/BaseCollection.php:217`

### jsonSerialize()

| Param      | Type               | Description |
| ---------- | ------------------ | ----------- |
| **Return** | _array&#124;mixed_ |             |

```php
public function BaseCollection::jsonSerialize(): mixed
```

File location: `src/BaseCollection.php:653`

### keys()

Return keys of all collection items.

The original collection will not be changed, a new collection will be returned instead.

| Param      | Type                                          | Description |
| ---------- | --------------------------------------------- | ----------- |
| **Return** | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function TypedCollection::keys(): mixed
```

File location: `src/TypedCollection.php:115`

### last()

Returns last item of the collection

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _mixed_ |             |

```php
public function BaseCollection::last(): mixed
```

File location: `src/BaseCollection.php:563`

### lastKey()

Returns last key of the collection

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _mixed_ |             |

```php
public function BaseCollection::lastKey(): mixed
```

File location: `src/BaseCollection.php:569`

### map()

Run a map over each of the items
Also collection will be downgraded

| Param       | Type                                          | Description |
| ----------- | --------------------------------------------- | ----------- |
| `$callback` | _callable_                                    |             |
| **Return**  | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function TypedCollection::map(callable $callback): mixed
```

File location: `src/TypedCollection.php:155`

### matching()

Filter collection matching given criteria.

The original collection will not be changed, a new collection will be returned instead.

| Param       | Type                                      | Description |
| ----------- | ----------------------------------------- | ----------- |
| `$criteria` | _\spaceonfire\Criteria\CriteriaInterface_ |             |
| **Return**  | _self_                                    |             |

```php
public function BaseCollection::matching(\spaceonfire\Criteria\CriteriaInterface $criteria): \spaceonfire\Collection\CollectionInterface
```

File location: `src/BaseCollection.php:503`

### max()

Calculate the maximum value of a field of the models in the collection.

| Param                      | Type                              | Description                                                      |
| -------------------------- | --------------------------------- | ---------------------------------------------------------------- |
| `$field`                   | _string&#124;callable&#124;array_ | the name of the field to calculate. This will be passed to       |
| `ArrayHelper::getValue()`. |
| **Return**                 | _int&#124;float&#124;null_        | the calculated maximum value. `null` if the collection is empty. |

```php
public function BaseCollection::max(mixed $field = null): mixed
```

File location: `src/BaseCollection.php:176`

### median()

Calculate the median value of items in the collection

| Param      | Type                       | Description                                                     |
| ---------- | -------------------------- | --------------------------------------------------------------- |
| `$field`   | _mixed_                    | the name of the field to calculate.                             |
| **Return** | _int&#124;float&#124;null_ | the calculated median value. `null` if the collection is empty. |

```php
public function BaseCollection::median(mixed $field = null): mixed
```

File location: `src/BaseCollection.php:133`

### merge()

Merge one or more arrays or collections with current collection.

Data in this collection will be overwritten if non-integer keys exist in the merged collection.

The original collection will not be changed, a new collection will be returned instead.

| Param          | Type                                          | Description |
| -------------- | --------------------------------------------- | ----------- |
| `$collections` | _iterable[]_                                  |             |
| **Return**     | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function BaseCollection::merge(mixed ...$collections): mixed
```

File location: `src/BaseCollection.php:345`

### min()

Calculate the minimum value of a field of the models in the collection

| Param                      | Type                              | Description                                                      |
| -------------------------- | --------------------------------- | ---------------------------------------------------------------- |
| `$field`                   | _string&#124;callable&#124;array_ | the name of the field to calculate. This will be passed to       |
| `ArrayHelper::getValue()`. |
| **Return**                 | _int&#124;float&#124;null_        | the calculated minimum value. `null` if the collection is empty. |

```php
public function BaseCollection::min(mixed $field = null): mixed
```

File location: `src/BaseCollection.php:199`

### offsetExists()

| Param     | Type    | Description |
| --------- | ------- | ----------- |
| `$offset` | _mixed_ |             |

```php
public function BaseCollection::offsetExists(mixed $offset): mixed
```

File location: `src/BaseCollection.php:584`

### offsetGet()

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| `$offset`  | _mixed_ |             |
| **Return** | _mixed_ |             |

```php
public function BaseCollection::offsetGet(mixed $offset): mixed
```

File location: `src/BaseCollection.php:594`

### offsetSet()

| Param     | Type              | Description |
| --------- | ----------------- | ----------- |
| `$offset` | _mixed&#124;null_ |             |
| `$value`  | _mixed_           |             |

```php
public function TypedCollection::offsetSet(mixed $offset, mixed $value): mixed
```

File location: `src/TypedCollection.php:99`

### offsetUnset()

| Param     | Type    | Description |
| --------- | ------- | ----------- |
| `$offset` | _mixed_ |             |

```php
public function BaseCollection::offsetUnset(mixed $offset): mixed
```

File location: `src/BaseCollection.php:617`

### reduce()

Reduce the collection to a single value.

| Param           | Type       | Description                                          |
| --------------- | ---------- | ---------------------------------------------------- |
| `$callback`     | _callable_ | the callback function to compute the reduce value.   |
| `$initialValue` | _mixed_    | initial value to pass to the callback on first item. |
| **Return**      | _mixed_    |                                                      |

```php
public function BaseCollection::reduce(callable $callback, mixed $initialValue = null): mixed
```

File location: `src/BaseCollection.php:165`

### remap()

Convert collection data by selecting a new key and a new value for each item.

Builds a map (key-value pairs) from a multidimensional array or an array of objects.
The `$from` and `$to` parameters specify the key names or property names to set up the map.
The original collection will not be changed, a new collection will be returned instead.
Also collection will be downgraded

| Param                      | Type                                          | Description                                                                          |
| -------------------------- | --------------------------------------------- | ------------------------------------------------------------------------------------ |
| `$from`                    | _string&#124;callable_                        | the field of the item to use as the key of the created map. This can be a callable   |
| that returns such a value. |
| `$to`                      | _string&#124;callable_                        | the field of the item to use as the value of the created map. This can be a callable |
| that returns such a value. |
| **Return**                 | _\spaceonfire\Collection\CollectionInterface_ |                                                                                      |

```php
public function TypedCollection::remap(mixed $from, mixed $to): mixed
```

File location: `src/TypedCollection.php:130`

### remove()

Remove a specific item from the collection.

The original collection will not be changed, a new collection with modified data is returned.

| Param                                                                                         | Type                                          | Description                                                                              |
| --------------------------------------------------------------------------------------------- | --------------------------------------------- | ---------------------------------------------------------------------------------------- |
| `$item`                                                                                       | _mixed&#124;callable_                         | the item to search for. You may also pass a callable that returns a boolean. The         |
| callable will be called on each item and in case it returns `true`, the item will be removed. |
| `$strict`                                                                                     | _bool_                                        | whether comparison should be compared strict (`===`) or not (`==`). Defaults to `false`. |
| **Return**                                                                                    | _\spaceonfire\Collection\CollectionInterface_ |                                                                                          |

```php
public function BaseCollection::remove(mixed $item, mixed $strict = false): mixed
```

File location: `src/BaseCollection.php:423`

### replace()

Replace a specific item in the collection with another one.

The original collection will not be changed, a new collection will be returned instead.

| Param          | Type                                          | Description                                                                              |
| -------------- | --------------------------------------------- | ---------------------------------------------------------------------------------------- |
| `$item`        | _mixed_                                       | the item to search for.                                                                  |
| `$replacement` | _mixed_                                       | the replacement to insert instead of the item.                                           |
| `$strict`      | _bool_                                        | whether comparison should be compared strict (`===`) or not (`==`). Defaults to `false`. |
| **Return**     | _\spaceonfire\Collection\CollectionInterface_ |                                                                                          |

```php
public function TypedCollection::replace(mixed $item, mixed $replacement, mixed $strict = true): mixed
```

File location: `src/TypedCollection.php:161`

### reverse()

Reverse the order of items.

The original collection will not be changed, a new collection will be returned instead.

| Param      | Type                                          | Description |
| ---------- | --------------------------------------------- | ----------- |
| **Return** | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function BaseCollection::reverse(): mixed
```

File location: `src/BaseCollection.php:306`

### slice()

Slice the set of elements by an offset and number of items to return.

The original collection will not be changed, a new collection will be returned instead.

| Param           | Type                                          | Description                                  |
| --------------- | --------------------------------------------- | -------------------------------------------- |
| `$offset`       | _int_                                         | starting offset for the slice.               |
| `$limit`        | _int&#124;null_                               | the number of elements to return at maximum. |
| `$preserveKeys` | _bool_                                        | whether to preserve item keys.               |
| **Return**      | _\spaceonfire\Collection\CollectionInterface_ |                                              |

```php
public function BaseCollection::slice(mixed $offset, mixed $limit = null, mixed $preserveKeys = true): mixed
```

File location: `src/BaseCollection.php:494`

### sort()

Sort collection data by value.

If the collection values are not scalar types, use `sortBy()` instead.
The original collection will not be changed, a new collection with sorted data is returned.

| Param        | Type  | Description                                                               |
| ------------ | ----- | ------------------------------------------------------------------------- |
| `$direction` | _int_ | sort direction, either `SORT_ASC` or `SORT_DESC`.                         |
| `$sortFlag`  | _int_ | type of comparison, either `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, |

`SORT_LOCALE_STRING`, `SORT_NATURAL` or `SORT_FLAG_CASE`. For details see
[the PHP manual](http://php.net/manual/en/function.sort.php#refsect1-function.sort-parameters).|
|**Return**|_\spaceonfire\Collection\CollectionInterface_||

```php
public function BaseCollection::sort(mixed $direction = SORT_ASC, mixed $sortFlag = SORT_REGULAR): mixed
```

File location: `src/BaseCollection.php:237`

### sortBy()

Sort collection data by one or multiple values.

Note that keys will not be preserved by this method.

This method uses `ArrayHelper::multisort()` on the collection data.

The original collection will not be changed, a new collection with sorted data is returned.

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
public function BaseCollection::sortBy(mixed $key, mixed $direction = SORT_ASC, mixed $sortFlag = SORT_REGULAR): mixed
```

File location: `src/BaseCollection.php:295`

### sortByKey()

Sort collection data by key.

The original collection will not be changed, a new collection with sorted data is returned.

| Param        | Type  | Description                                                               |
| ------------ | ----- | ------------------------------------------------------------------------- |
| `$direction` | _int_ | sort direction, either `SORT_ASC` or `SORT_DESC`.                         |
| `$sortFlag`  | _int_ | type of comparison, either `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, |

`SORT_LOCALE_STRING`, `SORT_NATURAL` or `SORT_FLAG_CASE`. For details see
[the PHP manual](http://php.net/manual/en/function.sort.php#refsect1-function.sort-parameters).|
|**Return**|_\spaceonfire\Collection\CollectionInterface_||

```php
public function BaseCollection::sortByKey(mixed $direction = SORT_ASC, mixed $sortFlag = SORT_REGULAR): mixed
```

File location: `src/BaseCollection.php:254`

### sortNatural()

Sort collection data by value using natural sort comparison.

If the collection values are not scalar types, use `sortBy()` instead.
The original collection will not be changed, a new collection with sorted data is returned.

| Param            | Type                                          | Description                                                                        |
| ---------------- | --------------------------------------------- | ---------------------------------------------------------------------------------- |
| `$caseSensitive` | _bool_                                        | whether comparison should be done in a case-sensitive manner. Defaults to `false`. |
| **Return**       | _\spaceonfire\Collection\CollectionInterface_ |                                                                                    |

```php
public function BaseCollection::sortNatural(mixed $caseSensitive = false): mixed
```

File location: `src/BaseCollection.php:271`

### sum()

Calculate the sum of a field of the models in the collection.

| Param                      | Type                                        | Description                                                |
| -------------------------- | ------------------------------------------- | ---------------------------------------------------------- |
| `$field`                   | _string&#124;callable&#124;array&#124;null_ | the name of the field to calculate. This will be passed to |
| `ArrayHelper::getValue()`. |
| **Return**                 | _int&#124;float_                            | the calculated sum.                                        |

```php
public function BaseCollection::sum(mixed $field = null): mixed
```

File location: `src/BaseCollection.php:109`

### toJson()

Get the collection of items as JSON.

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| `$options` | _int_    |             |
| **Return** | _string_ |             |

```php
public function BaseCollection::toJson(int $options): string
```

File location: `src/BaseCollection.php:625`

### unique()

Removes duplicate values from the collection
The original collection will not be changed, a new collection will be returned instead.

| Param        | Type                                          | Description                                                       |
| ------------ | --------------------------------------------- | ----------------------------------------------------------------- |
| `$sortFlags` | _int_                                         | sort flags argument for array_unique. Defaults to `SORT_REGULAR`. |
| **Return**   | _\spaceonfire\Collection\CollectionInterface_ |                                                                   |

```php
public function BaseCollection::unique(int $sortFlags = SORT_REGULAR): mixed
```

File location: `src/BaseCollection.php:526`

### values()

Return items without keys.

The original collection will not be changed, a new collection will be returned instead.

| Param      | Type                                          | Description |
| ---------- | --------------------------------------------- | ----------- |
| **Return** | _\spaceonfire\Collection\CollectionInterface_ |             |

```php
public function BaseCollection::values(): mixed
```

File location: `src/BaseCollection.php:315`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)
