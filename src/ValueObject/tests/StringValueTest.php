<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use PHPUnit\Framework\TestCase;

class StringValueTest extends TestCase
{
    private function factory($val): StringValue
    {
        return new class($val) extends StringValue {
        };
    }

    public function testConstruct(): void
    {
        $val = $this->factory('Hello');
        $this->assertEquals('Hello', $val->value());
        $this->assertEquals('Hello', (string)$val);
        $this->assertEquals('"Hello"', json_encode($val));
    }

    public function testConstructFromNumber(): void
    {
        $val = $this->factory(12345);
        $this->assertEquals(12345, $val->value());
        $this->assertEquals(12345, (string)$val);
        $this->assertEquals('"12345"', json_encode($val));
    }

    public function testConstructFailWithObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory(new \stdClass());
    }
}
