<?php

namespace spaceonfire\Collection;

use Closure;
use InvalidArgumentException;

class ArrayHelper
{
    /**
     * Convert a multi-dimensional array into a single-dimensional array
     * @param array $array Source multi-dimensional array
     * @param string $separator Glue string for imploding keys
     * @param string $prefix Key prefix, mostly needed for recursive call
     * @return array single-dimensional array
     */
    public static function flatten(array $array, $separator = '.', $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $item) {
            $prefixedKey = ($prefix ? $prefix . $separator : '') . $key;

            if (static::isArrayAssoc($item)) {
                $childFlaten = self::flatten($item, $separator, $prefixedKey);
                foreach ($childFlaten as $childKey => $childValue) {
                    $result[$childKey] = $childValue;
                }
            } else {
                $result[$prefixedKey] = $item;
            }
        }
        return $result;
    }

    /**
     * Check that array is associative (have at least one string key)
     * @param mixed $var variable to check
     * @return bool
     */
    public static function isArrayAssoc($var): bool
    {
        if (!is_array($var)) {
            return false;
        }

        $i = 0;
        foreach ($var as $k => $v) {
            if ('' . $k !== '' . $i) {
                return true;
            }
            $i++;
        }

        return false;
    }

    /**
     * Convert single-dimensional associative array to multi-dimensional by splitting keys with separator
     * @param array $array Source single-dimensional array
     * @param string $separator Glue string for exploding keys
     * @return array multi-dimensional array
     */
    public static function unflatten(array $array, $separator = '.'): array
    {
        $nestedKeys = array_filter(array_keys($array), function ($key) use ($separator) {
            return strpos($key, $separator) !== false;
        });
        if (!count($nestedKeys)) {
            return $array;
        }
        foreach ($nestedKeys as $key) {
            $prefix = explode($separator, $key);
            $field = array_pop($prefix);
            $prefix = implode($separator, $prefix);
            $array[$prefix][$field] = $array[$key];
            unset($array[$key]);
        }
        return self::unflatten($array, $separator);
    }

    /**
     * Recursive merge multiple arrays
     * @param array ...$arrays
     * @return array
     */
    public static function merge(...$arrays): array
    {
        foreach ($arrays as $array) {
            if (!is_array($array)) {
                throw new InvalidArgumentException('');
            }
        }

        $ret = array_shift($arrays);

        while (!empty($arrays)) {
            foreach (array_shift($arrays) as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $ret)) {
                        $ret[] = $v;
                    } else {
                        $ret[$k] = $v;
                    }
                } elseif (is_array($v) && isset($ret[$k]) && is_array($ret[$k])) {
                    $ret[$k] = static::merge($ret[$k], $v);
                } else {
                    $ret[$k] = $v;
                }
            }
        }

        return $ret;
    }

    /**
     * Writes a value into an associative array at the key path specified.
     * If there is no such key path yet, it will be created recursively.
     * If the key exists, it will be overwritten.
     *
     * ```php
     *  $array = [
     *      'key' => [
     *          'in' => [
     *              'val1',
     *              'key' => 'val'
     *          ]
     *      ]
     *  ];
     * ```
     *
     * The result of `ArrayHelper::setValue($array, 'key.in.0', ['arr' => 'val']);` will be the following:
     *
     * ```php
     *  [
     *      'key' => [
     *          'in' => [
     *              ['arr' => 'val'],
     *              'key' => 'val'
     *          ]
     *      ]
     *  ]
     *
     * ```
     *
     * The result of
     * `ArrayHelper::setValue($array, 'key.in', ['arr' => 'val']);` or
     * `ArrayHelper::setValue($array, ['key', 'in'], ['arr' => 'val']);`
     * will be the following:
     *
     * ```php
     *  [
     *      'key' => [
     *          'in' => [
     *              'arr' => 'val'
     *          ]
     *      ]
     *  ]
     * ```
     *
     * @param array $array the array to write the value to
     * @param string|array|null $path the path of where do you want to write a value to `$array`
     * the path can be described by a string when each key should be separated by a dot
     * you can also describe the path as an array of keys
     * if the path is null then `$array` will be assigned the `$value`
     * @param mixed $value the value to be written
     */
    public static function setValue(&$array, $path, $value)
    {
        if ($path === null) {
            $array = $value;
            return;
        }

        $keys = is_array($path) ? $path : explode('.', $path);

        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key])) {
                $array[$key] = [];
            }
            if (!is_array($array[$key])) {
                $array[$key] = [$array[$key]];
            }
            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

    /**
     * Builds a map (key-value pairs) from a multidimensional array or an array of objects.
     * The `$from` and `$to` parameters specify the key names or property names to set up the map.
     * Optionally, one can further group the map according to a grouping field `$group`.
     *
     * For example,
     *
     * ```php
     * $array = [
     *     ['id' => '123', 'name' => 'aaa', 'class' => 'x'],
     *     ['id' => '124', 'name' => 'bbb', 'class' => 'x'],
     *     ['id' => '345', 'name' => 'ccc', 'class' => 'y'],
     * ];
     *
     * $result = ArrayHelper::map($array, 'id', 'name');
     * // the result is:
     * // [
     * //     '123' => 'aaa',
     * //     '124' => 'bbb',
     * //     '345' => 'ccc',
     * // ]
     *
     * $result = ArrayHelper::map($array, 'id', 'name', 'class');
     * // the result is:
     * // [
     * //     'x' => [
     * //         '123' => 'aaa',
     * //         '124' => 'bbb',
     * //     ],
     * //     'y' => [
     * //         '345' => 'ccc',
     * //     ],
     * // ]
     * ```
     *
     * @param array $array
     * @param string|Closure $from
     * @param string|Closure $to
     * @param string|Closure $group
     * @return array
     */
    public static function map($array, $from, $to, $group = null)
    {
        $result = [];
        foreach ($array as $element) {
            $key = static::getValue($element, $from);
            $value = static::getValue($element, $to);
            if ($group !== null) {
                $result[static::getValue($element, $group)][$key] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     * If the key does not exist in the array, the default value will be returned instead.
     * Not used when getting value from an object.
     *
     * The key may be specified in a dot format to retrieve the value of a sub-array or the property
     * of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
     * be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
     * or `$array->x` is neither an array nor an object, the default value will be returned.
     * Note that if the array already has an element `x.y.z`, then its value will be returned
     * instead of going through the sub-arrays. So it is better to be done specifying an array of key names
     * like `['x', 'y', 'z']`.
     *
     * Below are some usage examples,
     *
     * ```php
     * // working with array
     * $username = ArrayHelper::getValue($_POST, 'username');
     * // working with object
     * $username = ArrayHelper::getValue($user, 'username');
     * // working with anonymous function
     * $fullName = ArrayHelper::getValue($user, function ($user, $defaultValue) {
     *     return $user->firstName . ' ' . $user->lastName;
     * });
     * // using dot format to retrieve the property of embedded object
     * $street = ArrayHelper::getValue($users, 'address.street');
     * // using an array of keys to retrieve the value
     * $value = ArrayHelper::getValue($versions, ['1.0', 'date']);
     * ```
     *
     * @param array|object $array array or object to extract value from
     * @param string|Closure|array $key key name of the array element, an array of keys or property name of the object,
     * or an anonymous function returning the value. The anonymous function signature should be:
     * `function($array, $defaultValue)`.
     * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
     * getting value from an object.
     * @return mixed the value of the element if found, default value otherwise
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof Closure) {
            return $key($array, $default);
        }

        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            // this is expected to fail if the property does not exist, or __get() is not implemented
            // it is not reliably possible to check whether a property is accessible beforehand
            return $array->$key;
        }

        if (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        }

        return $default;
    }

    /**
     * Sorts an array of objects or arrays (with the same structure) by one or several keys.
     * @param array $array the array to be sorted. The array will be modified after calling this method.
     * @param string|Closure|string[] $key the key(s) to be sorted by. This refers to a key name of the sub-array
     * elements, a property name of the objects, or an anonymous function returning the values for comparison
     * purpose. The anonymous function signature should be: `function($item)`.
     * To sort by multiple keys, provide an array of keys here.
     * @param int|array $direction the sorting direction. It can be either `SORT_ASC` or `SORT_DESC`.
     * When sorting by multiple keys with different sorting directions, use an array of sorting directions.
     * @param int|array $sortFlag the PHP sort flag. Valid values include
     * `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, `SORT_LOCALE_STRING`, `SORT_NATURAL` and `SORT_FLAG_CASE`.
     * Please refer to [PHP manual](https://secure.php.net/manual/en/function.sort.php)
     * for more details. When sorting by multiple keys with different sort flags, use an array of sort flags.
     * @throws InvalidArgumentException if the $direction or $sortFlag parameters do not have
     * correct number of elements as that of $key.
     */
    public static function multisort(&$array, $key, $direction = SORT_ASC, $sortFlag = SORT_REGULAR)
    {
        $keys = is_array($key) ? $key : [$key];
        if (empty($keys) || empty($array)) {
            return;
        }
        $n = count($keys);
        if (is_scalar($direction)) {
            $direction = array_fill(0, $n, $direction);
        } elseif (count($direction) !== $n) {
            throw new InvalidArgumentException('The length of $direction parameter must be the same as that of $keys.');
        }
        if (is_scalar($sortFlag)) {
            $sortFlag = array_fill(0, $n, $sortFlag);
        } elseif (count($sortFlag) !== $n) {
            throw new InvalidArgumentException('The length of $sortFlag parameter must be the same as that of $keys.');
        }
        $args = [];
        foreach ($keys as $i => $k) {
            $flag = $sortFlag[$i];
            $args[] = static::getColumn($array, $k);
            $args[] = $direction[$i];
            $args[] = $flag;
        }

        // This fix is used for cases when main sorting specified by columns has equal values
        // Without it it will lead to Fatal Error: Nesting level too deep - recursive dependency?
        $args[] = range(1, count($array));
        $args[] = SORT_ASC;
        $args[] = SORT_NUMERIC;

        $args[] = &$array;
        array_multisort(...$args);
    }

    /**
     * Returns the values of a specified column in an array.
     * The input array should be multidimensional or an array of objects.
     *
     * For example,
     *
     * ```php
     * $array = [
     *     ['id' => '123', 'data' => 'abc'],
     *     ['id' => '345', 'data' => 'def'],
     * ];
     * $result = ArrayHelper::getColumn($array, 'id');
     * // the result is: ['123', '345']
     * ```
     *
     * @param array $array
     * @param int|string|Closure $name
     * @param bool $keepKeys
     * @return array
     */
    public static function getColumn($array, $name, $keepKeys = true): array
    {
        $result = [];
        if ($keepKeys) {
            foreach ($array as $k => $element) {
                $result[$k] = static::getValue($element, $name);
            }
        } else {
            foreach ($array as $element) {
                $result[] = static::getValue($element, $name);
            }
        }

        return $result;
    }
}
