<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use PHPUnit\Framework\TestCase;

class IntValueTest extends TestCase
{
    private function factory($val): IntValue
    {
        return new class ($val) extends IntValue {
        };
    }

    public function testConstructor()
    {
        $val = $this->factory(5);
        $this->assertEquals(5, $val->value());
        $this->assertEquals('5', (string)$val);
    }

    public function testEqualsTo()
    {
        $a = $this->factory(5);
        $b = $this->factory(5);
        $c = $this->factory(10);
        $this->assertTrue($a->equalsTo($b));
        $this->assertFalse($a->equalsTo($c));
    }

    public function testIsBiggerThat()
    {
        $a = $this->factory(5);
        $b = $this->factory(1);
        $c = $this->factory(10);
        $this->assertTrue($a->isBiggerThan($b));
        $this->assertFalse($a->isBiggerThan($c));
    }

    public function testJson()
    {
        $val = $this->factory(5);
        $this->assertEquals(5, json_encode($val));
    }
}
