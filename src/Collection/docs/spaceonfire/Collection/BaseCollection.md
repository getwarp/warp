# Class BaseCollection

Abstract class `BaseCollection` contains base implementation of `CollectionInterface`.

Use it for building your custom collection classes.

- Full name: `\spaceonfire\Collection\BaseCollection`
- This class implements:
    - `\spaceonfire\Collection\CollectionInterface`
    - `\JsonSerializable`

## Methods

### __construct()

BaseCollection constructor.

|Param|Type|Description|
|---|---|---|
|`$items`|*array*||

```php
public function BaseCollection::__construct(mixed $items = []): mixed
```

File location: `src/BaseCollection.php:31`

### __toString()

Convert the collection to its string representation.

|Param|Type|Description|
|---|---|---|
|**Return**|*string*||

```php
public function BaseCollection::__toString(): mixed
```

File location: `src/BaseCollection.php:494`

### all()

Get all of the items in the collection.

|Param|Type|Description|
|---|---|---|
|**Return**|*array*||

```php
public function BaseCollection::all(): array
```

File location: `src/BaseCollection.php:73`

### contains()

Check whether the collection contains a specific item.

|Param|Type|Description|
|---|---|---|
|`$item`|*mixed&#124;\Closure*|the item to search for. You may also pass a closure that returns a boolean.
The closure will be called on each item and in case it returns `true`, the item will be considered to
be found. In case a closure is passed, `$strict` parameter has no effect.|
|`$strict`|*bool*|whether comparison should be compared strict (`===`) or not (`==`).
Defaults to `false`.|
|**Return**|*bool*|`true` if the collection contains at least one item that matches, `false` if not.|

```php
public function BaseCollection::contains(mixed $item, mixed $strict = false): bool
```

File location: `src/BaseCollection.php:347`

### count()


```php
public function BaseCollection::count(): mixed
```

File location: `src/BaseCollection.php:168`

### each()

Execute a callback over each item.

|Param|Type|Description|
|---|---|---|
|`$callback`|*callable*||
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::each(callable $callback): mixed
```

File location: `src/BaseCollection.php:79`

### filter()

Filter items from the collection.

The original collection will not be changed, a new collection with modified data is returned.

|Param|Type|Description|
|---|---|---|
|`$callback`|*callable&#124;null*|the callback function to decide which items to remove.|
|**Return**|*\spaceonfire\Collection\CollectionInterface*|a new collection containing the filtered items.|

```php
public function BaseCollection::filter(callable $callback = null): mixed
```

File location: `src/BaseCollection.php:405`

### find()

Find item in the collection

|Param|Type|Description|
|---|---|---|
|`$callback`|*callable*|Testing function|
|**Return**|*mixed&#124;null*|First element that satisfies provided `$callback` or `null`|

```php
public function BaseCollection::find(callable $callback): mixed
```

File location: `src/BaseCollection.php:411`

### flip()

Flip keys and values of all collection items.

The original collection will not be changed, a new collection will be returned instead.

|Param|Type|Description|
|---|---|---|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::flip(): mixed
```

File location: `src/BaseCollection.php:275`

### getIterator()


```php
public function BaseCollection::getIterator(): mixed
```

File location: `src/BaseCollection.php:457`

### groupBy()

Group items by a specified field.

The original collection will not be changed, a new collection will be returned instead.

|Param|Type|Description|
|---|---|---|
|`$groupField`|*string&#124;\Closure*|the field of the item to use as the group value.
This can be a closure that returns such a value.|
|`$preserveKeys`|*bool*|whether to preserve item keys in the groups. Defaults to `true`.|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::groupBy(mixed $groupField, mixed $preserveKeys = true): mixed
```

File location: `src/BaseCollection.php:321`

### indexBy()

Assign a new key to each item in the collection.

The original collection will not be changed, a new collection will be returned instead.

|Param|Type|Description|
|---|---|---|
|`$key`|*string&#124;\Closure*|the field of the item to use as the new key.
This can be a closure that returns such a value.|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::indexBy(mixed $key): mixed
```

File location: `src/BaseCollection.php:301`

### isEmpty()

Determine if the collection is empty or not.

|Param|Type|Description|
|---|---|---|
|**Return**|*bool*||

```php
public function BaseCollection::isEmpty(): bool
```

File location: `src/BaseCollection.php:162`

### jsonSerialize()


```php
public function BaseCollection::jsonSerialize(): mixed
```

File location: `src/BaseCollection.php:510`

### keys()

Return keys of all collection items.

The original collection will not be changed, a new collection will be returned instead.

|Param|Type|Description|
|---|---|---|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::keys(): mixed
```

File location: `src/BaseCollection.php:266`

### map()

Run a map over each of the items

|Param|Type|Description|
|---|---|---|
|`$callback`|*callable*||
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::map(callable $callback): mixed
```

File location: `src/BaseCollection.php:440`

### max()

Calculate the maximum value of a field of the models in the collection.

|Param|Type|Description|
|---|---|---|
|`$field`|*string&#124;\Closure&#124;array*|the name of the field to calculate.
This will be passed to [[ArrayHelper::getValue()]].|
|**Return**|*int&#124;float&#124;null*|the calculated maximum value. `null` if the collection is empty.|

```php
public function BaseCollection::max(mixed $field = null): mixed
```

File location: `src/BaseCollection.php:121`

### merge()

Merge one or more arrays or collections with current collection.

Data in this collection will be overwritten if non-integer keys exist in the merged collection.

The original collection will not be changed, a new collection will be returned instead.

|Param|Type|Description|
|---|---|---|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::merge(mixed ...$collections): mixed
```

File location: `src/BaseCollection.php:287`

### min()

Calculate the minimum value of a field of the models in the collection

|Param|Type|Description|
|---|---|---|
|`$field`|*string&#124;\Closure&#124;array*|the name of the field to calculate.
This will be passed to [[ArrayHelper::getValue()]].|
|**Return**|*int&#124;float&#124;null*|the calculated minimum value. `null` if the collection is empty.|

```php
public function BaseCollection::min(mixed $field = null): mixed
```

File location: `src/BaseCollection.php:144`

### offsetExists()


```php
public function BaseCollection::offsetExists(mixed $offset): mixed
```

File location: `src/BaseCollection.php:463`

### offsetGet()


```php
public function BaseCollection::offsetGet(mixed $offset): mixed
```

File location: `src/BaseCollection.php:469`

### offsetSet()


```php
public function BaseCollection::offsetSet(mixed $offset, mixed $value): mixed
```

File location: `src/BaseCollection.php:475`

### offsetUnset()


```php
public function BaseCollection::offsetUnset(mixed $offset): mixed
```

File location: `src/BaseCollection.php:485`

### reduce()

Reduce the collection to a single value.

|Param|Type|Description|
|---|---|---|
|`$callback`|*callable*|the callback function to compute the reduce value.|
|`$initialValue`|*mixed*|initial value to pass to the callback on first item.|
|**Return**|*mixed*||

```php
public function BaseCollection::reduce(callable $callback, mixed $initialValue = null): mixed
```

File location: `src/BaseCollection.php:110`

### remap()

Convert collection data by selecting a new key and a new value for each item.

Builds a map (key-value pairs) from a multidimensional array or an array of objects.
The `$from` and `$to` parameters specify the key names or property names to set up the map.
The original collection will not be changed, a new collection will be returned instead.

|Param|Type|Description|
|---|---|---|
|`$from`|*string&#124;\Closure*|the field of the item to use as the key of the created map.
This can be a closure that returns such a value.|
|`$to`|*string&#124;\Closure*|the field of the item to use as the value of the created map.
This can be a closure that returns such a value.|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::remap(mixed $from, mixed $to): mixed
```

File location: `src/BaseCollection.php:312`

### remove()

Remove a specific item from the collection.

The original collection will not be changed, a new collection with modified data is returned.

|Param|Type|Description|
|---|---|---|
|`$item`|*mixed&#124;\Closure*|the item to search for. You may also pass a closure that returns a boolean.
The closure will be called on each item and in case it returns `true`, the item will be removed.
In case a closure is passed, `$strict` parameter has no effect.|
|`$strict`|*bool*|whether comparison should be compared strict (`===`) or not (`==`).
Defaults to `false`.|
|**Return**|*\spaceonfire\Collection\CollectionInterface*|a new collection containing the filtered items.|

```php
public function BaseCollection::remove(mixed $item, mixed $strict = false): mixed
```

File location: `src/BaseCollection.php:379`

### replace()

Replace a specific item in the collection with another one.

|Param|Type|Description|
|---|---|---|
|`$item`|*mixed*|the item to search for.|
|`$replacement`|*mixed*|the replacement to insert instead of the item.|
|`$strict`|*bool*|whether comparison should be compared strict (`===`) or not (`==`).
Defaults to `false`.
The original collection will not be changed, a new collection will be returned instead.|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::replace(mixed $item, mixed $replacement, mixed $strict = false): mixed
```

File location: `src/BaseCollection.php:428`

### reverse()

Reverse the order of items.

The original collection will not be changed, a new collection will be returned instead.

|Param|Type|Description|
|---|---|---|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::reverse(): mixed
```

File location: `src/BaseCollection.php:248`

### slice()

Slice the set of elements by an offset and number of items to return.

The original collection will not be changed, a new collection will be returned instead.

|Param|Type|Description|
|---|---|---|
|`$offset`|*int*|starting offset for the slice.|
|`$limit`|*int&#124;null*|the number of elements to return at maximum.|
|`$preserveKeys`|*bool*|whether to preserve item keys.|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::slice(mixed $offset, mixed $limit = null, mixed $preserveKeys = true): mixed
```

File location: `src/BaseCollection.php:451`

### sort()

Sort collection data by value.

If the collection values are not scalar types, use `sortBy()` instead.
The original collection will not be changed, a new collection with sorted data is returned.

|Param|Type|Description|
|---|---|---|
|`$direction`|*int*|sort direction, either `SORT_ASC` or `SORT_DESC`.|
|`$sortFlag`|*int*|type of comparison, either `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`,
`SORT_LOCALE_STRING`, `SORT_NATURAL` or `SORT_FLAG_CASE`.
See [the PHP manual](http://php.net/manual/en/function.sort.php#refsect1-function.sort-parameters)
for details.|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::sort(mixed $direction = SORT_ASC, mixed $sortFlag = SORT_REGULAR): mixed
```

File location: `src/BaseCollection.php:179`

### sortBy()

Sort collection data by one or multiple values.

Note that keys will not be preserved by this method.

This method uses `ArrayHelper::multisort()` on the collection data.

The original collection will not be changed, a new collection with sorted data is returned.

|Param|Type|Description|
|---|---|---|
|`$key`|*string&#124;\Closure&#124;array*|the key(s) to be sorted by. This refers to a key name of the sub-array
elements, a property name of the objects, or an anonymous function returning the values for comparison
purpose. The anonymous function signature should be: `function($item)`.
To sort by multiple keys, provide an array of keys here.|
|`$direction`|*int&#124;array*|the sorting direction. It can be either `SORT_ASC` or `SORT_DESC`.
When sorting by multiple keys with different sorting directions, use an array of sorting directions.|
|`$sortFlag`|*int&#124;array*|the PHP sort flag. Valid values include
`SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, `SORT_LOCALE_STRING`, `SORT_NATURAL` and `SORT_FLAG_CASE`.
Please refer to the [PHP manual](http://php.net/manual/en/function.sort.php)
for more details. When sorting by multiple keys with different sort flags, use an array of sort flags.|
|**Return**|*static*|a new collection containing the sorted items.|

```php
public function BaseCollection::sortBy(mixed $key, mixed $direction = SORT_ASC, mixed $sortFlag = SORT_REGULAR): mixed
```

File location: `src/BaseCollection.php:237`

### sortByKey()

Sort collection data by key.

The original collection will not be changed, a new collection with sorted data is returned.

|Param|Type|Description|
|---|---|---|
|`$direction`|*int*|sort direction, either `SORT_ASC` or `SORT_DESC`.|
|`$sortFlag`|*int*|type of comparison, either `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`,
`SORT_LOCALE_STRING`, `SORT_NATURAL` or `SORT_FLAG_CASE`.
See [the PHP manual](http://php.net/manual/en/function.sort.php#refsect1-function.sort-parameters)
for details.|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::sortByKey(mixed $direction = SORT_ASC, mixed $sortFlag = SORT_REGULAR): mixed
```

File location: `src/BaseCollection.php:196`

### sortNatural()

Sort collection data by value using natural sort comparison.

If the collection values are not scalar types, use `sortBy()` instead.
The original collection will not be changed, a new collection with sorted data is returned.

|Param|Type|Description|
|---|---|---|
|`$caseSensitive`|*bool*|whether comparison should be done in a case-sensitive manner. Defaults to `false`.|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::sortNatural(mixed $caseSensitive = false): mixed
```

File location: `src/BaseCollection.php:213`

### sum()

Calculate the sum of a field of the models in the collection.

|Param|Type|Description|
|---|---|---|
|`$field`|*string&#124;\Closure&#124;array&#124;null*|the name of the field to calculate.
This will be passed to [[ArrayHelper::getValue()]].|
|**Return**|*int&#124;float*|the calculated sum.|

```php
public function BaseCollection::sum(mixed $field = null): mixed
```

File location: `src/BaseCollection.php:96`

### toJson()

Get the collection of items as JSON.

|Param|Type|Description|
|---|---|---|
|`$options`|*int*||
|**Return**|*string*||

```php
public function BaseCollection::toJson(mixed $options): string
```

File location: `src/BaseCollection.php:504`

### values()

Return items without keys.

The original collection will not be changed, a new collection will be returned instead.

|Param|Type|Description|
|---|---|---|
|**Return**|*\spaceonfire\Collection\CollectionInterface*||

```php
public function BaseCollection::values(): mixed
```

File location: `src/BaseCollection.php:257`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)