<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use PHPUnit\Framework\TestCase;

class StringValueTest extends TestCase
{
    private function factory($val): StringValue
    {
        return new class ($val) extends StringValue {
        };
    }

    public function testConstructor()
    {
        $val = $this->factory('Hello');
        $this->assertEquals('Hello', $val->value());
        $this->assertEquals('Hello', (string)$val);
        $this->assertEquals('"Hello"', json_encode($val));
    }
}
