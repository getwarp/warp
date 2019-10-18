# Class ArrayHelper

- Full name: `\spaceonfire\Collection\ArrayHelper`

## Methods

### flatten()

Convert a multi-dimensional array into a single-dimensional array

|Param|Type|Description|
|---|---|---|
|`$array`|*array*|Source multi-dimensional array|
|`$separator`|*string*|Glue string for imploding keys|
|`$prefix`|*string*|Key prefix, mostly needed for recursive call|
|**Return**|*array*|single-dimensional array|

```php
public static function ArrayHelper::flatten(array $array, mixed $separator = ., mixed $prefix): array
```

File location: `src/ArrayHelper.php:17`

### getColumn()

Returns the values of a specified column in an array.

The input array should be multidimensional or an array of objects.

For example,

```php
$array = [
    ['id' => '123', 'data' => 'abc'],
    ['id' => '345', 'data' => 'def'],
];
$result = ArrayHelper::getColumn($array, 'id');
// the result is: ['123', '345']
```

|Param|Type|Description|
|---|---|---|
|`$array`|*array*||
|`$name`|*int&#124;string&#124;\Closure*||
|`$keepKeys`|*bool*||
|**Return**|*array*||

```php
public static function ArrayHelper::getColumn(mixed $array, mixed $name, mixed $keepKeys = true): array
```

File location: `src/ArrayHelper.php:392`

### getValue()

Retrieves the value of an array element or object property with the given key or property name.

If the key does not exist in the array, the default value will be returned instead.
Not used when getting value from an object.

The key may be specified in a dot format to retrieve the value of a sub-array or the property
of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
or `$array->x` is neither an array nor an object, the default value will be returned.
Note that if the array already has an element `x.y.z`, then its value will be returned
instead of going through the sub-arrays. So it is better to be done specifying an array of key names
like `['x', 'y', 'z']`.

Below are some usage examples,

```php
// working with array
$username = ArrayHelper::getValue($_POST, 'username');
// working with object
$username = ArrayHelper::getValue($user, 'username');
// working with anonymous function
$fullName = ArrayHelper::getValue($user, function ($user, $defaultValue) {
    return $user->firstName . ' ' . $user->lastName;
});
// using dot format to retrieve the property of embedded object
$street = ArrayHelper::getValue($users, 'address.street');
// using an array of keys to retrieve the value
$value = ArrayHelper::getValue($versions, ['1.0', 'date']);
```

|Param|Type|Description|
|---|---|---|
|`$array`|*array&#124;object*|array or object to extract value from|
|`$key`|*string&#124;\Closure&#124;array*|key name of the array element, an array of keys or property name of the object,
or an anonymous function returning the value. The anonymous function signature should be:
`function($array, $defaultValue)`.|
|`$default`|*mixed*|the default value to be returned if the specified array key does not exist. Not used when
getting value from an object.|
|**Return**|*mixed*|the value of the element if found, default value otherwise|

```php
public static function ArrayHelper::getValue(mixed $array, mixed $key, mixed $default = null): mixed
```

File location: `src/ArrayHelper.php:285`

### isArrayAssoc()

Check that array is associative (have at least one string key)

|Param|Type|Description|
|---|---|---|
|`$var`|*mixed*|variable to check|
|**Return**|*bool*||

```php
public static function ArrayHelper::isArrayAssoc(mixed $var): bool
```

File location: `src/ArrayHelper.php:40`

### map()

Builds a map (key-value pairs) from a multidimensional array or an array of objects.

The `$from` and `$to` parameters specify the key names or property names to set up the map.
Optionally, one can further group the map according to a grouping field `$group`.

For example,

```php
$array = [
    ['id' => '123', 'name' => 'aaa', 'class' => 'x'],
    ['id' => '124', 'name' => 'bbb', 'class' => 'x'],
    ['id' => '345', 'name' => 'ccc', 'class' => 'y'],
];

$result = ArrayHelper::map($array, 'id', 'name');
// the result is:
// [
//     '123' => 'aaa',
//     '124' => 'bbb',
//     '345' => 'ccc',
// ]

$result = ArrayHelper::map($array, 'id', 'name', 'class');
// the result is:
// [
//     'x' => [
//         '123' => 'aaa',
//         '124' => 'bbb',
//     ],
//     'y' => [
//         '345' => 'ccc',
//     ],
// ]
```

|Param|Type|Description|
|---|---|---|
|`$array`|*array*||
|`$from`|*string&#124;\Closure*||
|`$to`|*string&#124;\Closure*||
|`$group`|*string&#124;\Closure*||
|**Return**|*array*||

```php
public static function ArrayHelper::map(mixed $array, mixed $from, mixed $to, mixed $group = null): mixed
```

File location: `src/ArrayHelper.php:231`

### merge()

Recursive merge multiple arrays

|Param|Type|Description|
|---|---|---|
|`...$arrays`|*array*||
|**Return**|*array*||

```php
public static function ArrayHelper::merge(mixed ...$arrays): array
```

File location: `src/ArrayHelper.php:86`

### multisort()

Sorts an array of objects or arrays (with the same structure) by one or several keys.

|Param|Type|Description|
|---|---|---|
|`$array`|*array*|the array to be sorted. The array will be modified after calling this method.|
|`$key`|*string&#124;\Closure&#124;string[]*|the key(s) to be sorted by. This refers to a key name of the sub-array
elements, a property name of the objects, or an anonymous function returning the values for comparison
purpose. The anonymous function signature should be: `function($item)`.
To sort by multiple keys, provide an array of keys here.|
|`$direction`|*int&#124;array*|the sorting direction. It can be either `SORT_ASC` or `SORT_DESC`.
When sorting by multiple keys with different sorting directions, use an array of sorting directions.|
|`$sortFlag`|*int&#124;array*|the PHP sort flag. Valid values include
`SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, `SORT_LOCALE_STRING`, `SORT_NATURAL` and `SORT_FLAG_CASE`.
Please refer to [PHP manual](https://secure.php.net/manual/en/function.sort.php)
for more details. When sorting by multiple keys with different sort flags, use an array of sort flags.|

```php
public static function ArrayHelper::multisort(mixed &$array, mixed $key, mixed $direction = SORT_ASC, mixed $sortFlag = SORT_REGULAR): mixed
```

File location: `src/ArrayHelper.php:337`

### setValue()

Writes a value into an associative array at the key path specified.

If there is no such key path yet, it will be created recursively.
If the key exists, it will be overwritten.

```php
 $array = [
     'key' => [
         'in' => [
             'val1',
             'key' => 'val'
         ]
     ]
 ];
```

The result of `ArrayHelper::setValue($array, 'key.in.0', ['arr' => 'val']);` will be the following:

```php
 [
     'key' => [
         'in' => [
             ['arr' => 'val'],
             'key' => 'val'
         ]
     ]
 ]

```

The result of
`ArrayHelper::setValue($array, 'key.in', ['arr' => 'val']);` or
`ArrayHelper::setValue($array, ['key', 'in'], ['arr' => 'val']);`
will be the following:

```php
 [
     'key' => [
         'in' => [
             'arr' => 'val'
         ]
     ]
 ]
```

|Param|Type|Description|
|---|---|---|
|`$array`|*array*|the array to write the value to|
|`$path`|*string&#124;array&#124;null*|the path of where do you want to write a value to `$array`
the path can be described by a string when each key should be separated by a dot
you can also describe the path as an array of keys
if the path is null then `$array` will be assigned the `$value`|
|`$value`|*mixed*|the value to be written|

```php
public static function ArrayHelper::setValue(mixed &$array, mixed $path, mixed $value): mixed
```

File location: `src/ArrayHelper.php:167`

### unflatten()

Convert single-dimensional associative array to multi-dimensional by splitting keys with separator

|Param|Type|Description|
|---|---|---|
|`$array`|*array*|Source single-dimensional array|
|`$separator`|*string*|Glue string for exploding keys|
|**Return**|*array*|multi-dimensional array|

```php
public static function ArrayHelper::unflatten(array $array, mixed $separator = .): array
```

File location: `src/ArrayHelper.php:63`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)