<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Helpers;

use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    private function stringableObjectFactory(string $value)
    {
        return new class ($value) {
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

    public function testStringify()
    {
        $this->assertEquals('"a"', StringHelper::stringify('a'));
        $this->assertEquals('null', StringHelper::stringify(null));
        $this->assertEquals('false', StringHelper::stringify(false));
        $this->assertEquals('true', StringHelper::stringify(true));
        $this->assertEquals('5', StringHelper::stringify(5));
        $this->assertEquals('5.5', StringHelper::stringify(5.5));
        $this->assertEquals(null, StringHelper::stringify(new \stdClass()));
        $this->assertEquals('"/\\\\Привет\""', StringHelper::stringify('/\Привет"'));
        $this->assertEquals('"a"', StringHelper::stringify($this->stringableObjectFactory('a')));
        $this->assertEquals('[1, 2, 3, "a", "b", "c"]', StringHelper::stringify([1, 2, 3, 'a', 'b', 'c']));
        $this->assertEquals('[1 => "a", 2 => "b", 3 => "c"]', StringHelper::stringify([1 => 'a', 2 => 'b', 3 => 'c']));
    }

    /**
     * @see https://github.com/nette/utils/blob/master/tests/Utils/Helpers.getSuggestion().phpt
     */
    public function testGetSuggestion()
    {
        $this->assertEquals(null, StringHelper::getSuggestion([], ''));
        $this->assertEquals(null, StringHelper::getSuggestion([], 'a'));
        $this->assertEquals(null, StringHelper::getSuggestion(['a'], 'a'));
        $this->assertEquals('a', StringHelper::getSuggestion(['a', 'b'], ''));
        $this->assertEquals('b', StringHelper::getSuggestion(['a', 'b'], 'a')); // ignore 100% match
        $this->assertEquals('a1', StringHelper::getSuggestion(['a1', 'a2'], 'a')); // take first
        $this->assertEquals(null, StringHelper::getSuggestion(['aaa', 'bbb'], 'a'));
        $this->assertEquals(null, StringHelper::getSuggestion(['aaa', 'bbb'], 'ab'));
        $this->assertEquals(null, StringHelper::getSuggestion(['aaa', 'bbb'], 'abc'));
        $this->assertEquals('bar', StringHelper::getSuggestion(['foo', 'bar', 'baz'], 'baz'));
        $this->assertEquals('abcd', StringHelper::getSuggestion(['abcd'], 'acbd'));
        $this->assertEquals('abcd', StringHelper::getSuggestion(['abcd'], 'axbd'));
        $this->assertEquals(null, StringHelper::getSuggestion(['abcd'], 'axyd')); // 'tags' vs 'this'
        $this->assertEquals(null, StringHelper::getSuggestion(['setItem'], 'item'));
    }
}
