<?php

declare(strict_types=1);

namespace Warp\ValueObject\Helpers;

abstract class StringHelper
{
    /**
     * Stringify value
     * @param mixed $value
     * @return string|null stringified value or null
     */
    public static function stringify($value): ?string
    {
        if (is_array($value)) {
            $keys = array_keys($value);
            $useKeys = $keys !== range(0, count($keys) - 1);

            $ret = '';

            foreach ($value as $k => $v) {
                if ('' !== $ret) {
                    $ret .= ', ';
                }
                if ($useKeys) {
                    $ret .= $k . ' => ';
                }
                $ret .= self::stringify($v);
            }

            return '[' . $ret . ']';
        }

        if (is_object($value)) {
            if (!method_exists($value, '__toString')) {
                return null;
            }

            $value = (string)$value;
        }

        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: null;
    }

    /**
     * Finds the best suggestion (for 8-bit encoding).
     * @param string[]|mixed[] $possibilities
     * @param string $value
     * @return string|null
     * @see https://github.com/nette/utils/blob/master/src/Utils/Helpers.php#L58
     */
    public static function getSuggestion(array $possibilities, string $value): ?string
    {
        $best = null;
        $min = (strlen($value) / 4 + 1) * 10 + .1;
        foreach (array_unique($possibilities) as $item) {
            $item = (string)$item;
            if ($item !== $value && ($len = levenshtein($item, $value, 10, 11, 10)) < $min) {
                $min = $len;
                $best = $item;
            }
        }
        return $best;
    }
}
