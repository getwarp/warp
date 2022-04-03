<?php

declare(strict_types=1);

namespace Warp\ValueObject\Helpers;

use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    private function stringableObjectFactory(string $value)
    {
        return new class($value) {
            private $value;

            public function __construct(string $value)
            {
                $this->value = $value;
            }

            public function __toString()
            {
                return $this->value;
            }
        };
    }

    public function testStringify(): void
    {
        self::assertSame('"a"', StringHelper::stringify('a'));
        self::assertSame('null', StringHelper::stringify(null));
        self::assertSame('false', StringHelper::stringify(false));
        self::assertSame('true', StringHelper::stringify(true));
        self::assertSame('5', StringHelper::stringify(5));
        self::assertSame('5.5', StringHelper::stringify(5.5));
        self::assertSame(null, StringHelper::stringify(new \stdClass()));
        self::assertSame('"/\\\\Привет\""', StringHelper::stringify('/\Привет"'));
        self::assertSame('"a"', StringHelper::stringify($this->stringableObjectFactory('a')));
        self::assertSame('[1, 2, 3, "a", "b", "c"]', StringHelper::stringify([1, 2, 3, 'a', 'b', 'c']));
        self::assertSame('[1 => "a", 2 => "b", 3 => "c"]', StringHelper::stringify([1 => 'a', 2 => 'b', 3 => 'c']));
    }

    /**
     * @see https://github.com/nette/utils/blob/master/tests/Utils/Helpers.getSuggestion().phpt
     */
    public function testGetSuggestion(): void
    {
        self::assertSame(null, StringHelper::getSuggestion([], ''));
        self::assertSame(null, StringHelper::getSuggestion([], 'a'));
        self::assertSame(null, StringHelper::getSuggestion(['a'], 'a'));
        self::assertSame('a', StringHelper::getSuggestion(['a', 'b'], ''));
        self::assertSame('b', StringHelper::getSuggestion(['a', 'b'], 'a')); // ignore 100% match
        self::assertSame('a1', StringHelper::getSuggestion(['a1', 'a2'], 'a')); // take first
        self::assertSame(null, StringHelper::getSuggestion(['aaa', 'bbb'], 'a'));
        self::assertSame(null, StringHelper::getSuggestion(['aaa', 'bbb'], 'ab'));
        self::assertSame(null, StringHelper::getSuggestion(['aaa', 'bbb'], 'abc'));
        self::assertSame('bar', StringHelper::getSuggestion(['foo', 'bar', 'baz'], 'baz'));
        self::assertSame('abcd', StringHelper::getSuggestion(['abcd'], 'acbd'));
        self::assertSame('abcd', StringHelper::getSuggestion(['abcd'], 'axbd'));
        self::assertSame(null, StringHelper::getSuggestion(['abcd'], 'axyd')); // 'tags' vs 'this'
        self::assertSame(null, StringHelper::getSuggestion(['setItem'], 'item'));
    }
}
