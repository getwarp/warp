<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use PHPUnit\Framework\TestCase;
use spaceonfire\ValueObject\Fixtures\FixtureString;

class StringValueTest extends TestCase
{
    private function factory($val): AbstractStringValue
    {
        return FixtureString::new($val);
    }

    public function testConstruct(): void
    {
        $val = $this->factory('Hello');
        self::assertSame('Hello', $val->value());
        self::assertSame('Hello', (string)$val);
        self::assertSame('"Hello"', json_encode($val, JSON_THROW_ON_ERROR));
    }

    public function testConstructFromNumber(): void
    {
        $val = $this->factory(12345);
        self::assertSame('12345', $val->value());
        self::assertSame('12345', (string)$val);
        self::assertSame('"12345"', json_encode($val, JSON_THROW_ON_ERROR));
    }

    public function testConstructFailWithObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory(new \stdClass());
    }

    public function testConstructWithStringable(): void
    {
        $stringable = new class('foo') {
            private string $string;

            public function __construct(string $string)
            {
                $this->string = $string;
            }

            public function __toString()
            {
                return $this->string;
            }
        };

        self::assertSame('foo', $this->factory($stringable)->value());
    }
}
