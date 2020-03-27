<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use PHPUnit\Framework\TestCase;

class EnumValueTest extends TestCase
{
    private function factory($val): EnumValue
    {
        return new class ($val) extends EnumValue {
            public const ONE = 1;
            public const TWO_WORDS = 2;
            public const MANY_MANY_WORDS = 3;
            public const ONE_STRING = '1';
        };
    }

    public function testConstructor()
    {
        $val = $this->factory(1);
        $this->assertEquals(1, $val->value());
        $this->assertEquals('1', (string)$val);
        $this->assertEquals('"1"', json_encode($val));
    }

    public function testConstructorException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory(4);
    }

    public function testConstructFailWithObject()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory(new \stdClass());
    }

    public function testEquals()
    {
        $a = $this->factory(1);
        $b = $this->factory(1);
        $c = $this->factory(2);

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));

        $b = $this->factory('1');
        $this->assertTrue($a->equals($b));
    }

    public function testRandom()
    {
        $enum = $this->factory(1);

        $this->assertContains($enum::randomValue(), $enum::values());
        $this->assertContains($enum::random()->value(), $enum::values());
    }

    public function testMagicCalls()
    {
        $enum = $this->factory(1);

        $a = $enum::one();
        $b = $enum::twoWords();

        $this->assertInstanceOf(EnumValue::class, $a);
        $this->assertInstanceOf(EnumValue::class, $b);

        $this->assertEquals(1, $a->value());
        $this->assertEquals(2, $b->value());
    }
}
