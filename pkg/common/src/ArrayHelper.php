<?php

declare(strict_types=1);

namespace spaceonfire\Common;

abstract class ArrayHelper
{
    /**
     * Check that array is associative (have at least one string key)
     * @param mixed $var variable to check
     * @return bool
     */
    public static function isArrayAssoc($var): bool
    {
        if (!\is_array($var)) {
            return false;
        }

        $i = 0;
        foreach ($var as $k => $_) {
            if ('' . $k !== '' . $i) {
                return true;
            }
            ++$i;
        }

        return false;
    }

    /**
     * Convert a multi-dimensional array into a single-dimensional array
     * @param array<string,mixed> $array Source multi-dimensional array
     * @param string $glue Glue string for imploding keys
     * @param string $prefix Key prefix, mostly needed for recursive call
     * @return array<string,mixed>
     */
    public static function flatten(array $array, string $glue = '.', string $prefix = ''): array
    {
        return \iterator_to_array(self::flattenGenerator($array, $glue, $prefix));
    }

    /**
     * Convert single-dimensional associative array to multi-dimensional by splitting keys with separator
     * @param array<string,mixed> $array Source single-dimensional array
     * @param non-empty-string $delimiter Glue string for exploding keys
     * @return array<string,mixed>
     */
    public static function unflatten(array $array, string $delimiter = '.'): array
    {
        $arrays = [];

        foreach (self::unflattenGenerator($array, $delimiter) as $offset => $value) {
            $arrays[] = [
                $offset => $value,
            ];
        }

        return \array_merge_recursive(...$arrays);
    }

    /**
     * @param array<array-key,mixed> $array
     * @param string $glue
     * @param string $prefix
     * @return \Generator<string,mixed>
     */
    private static function flattenGenerator(array $array, string $glue, string $prefix): \Generator
    {
        foreach ($array as $offset => $value) {
            $prefixedOffset = ('' !== $prefix ? $prefix . $glue : '') . $offset;

            if (static::isArrayAssoc($value)) {
                yield from self::flattenGenerator($value, $glue, $prefixedOffset);
                continue;
            }

            yield $prefixedOffset => $value;
        }
    }

    /**
     * @param array<array-key,mixed> $array
     * @param non-empty-string $delimiter
     * @return \Generator<string,mixed>
     */
    private static function unflattenGenerator(array $array, string $delimiter): \Generator
    {
        foreach ($array as $offset => $value) {
            /** @phpstan-var string $prefix */
            [$prefix, $subOffset] = \explode($delimiter, (string)$offset, 2) + ['', null];

            if (null === $subOffset) {
                yield $offset => $value;
                continue;
            }

            yield $prefix => \iterator_to_array(self::unflattenGenerator([
                $subOffset => $value,
            ], $delimiter));
        }
    }
}
