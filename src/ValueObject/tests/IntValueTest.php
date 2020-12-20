<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use PHPUnit\Framework\TestCase;

class IntValueTest extends TestCase
{
    private function factory($val): IntValue
    {
        return new class($val) extends IntValue {
        };
    }

    public function testConstructFromInt(): void
    {
        $val = $this->factory(5);
        self::assertSame(5, $val->value());
        self::assertSame('5', (string)$val);
    }

    public function testConstructFromString(): void
    {
        $val = $this->factory('5');
        self::assertSame(5, $val->value());
        self::assertSame('5', (string)$val);
    }

    public function testConstructFailWithObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory(new \stdClass());
    }

    public function testEquals(): void
    {
        $a = $this->factory(5);
        $b = $this->factory(5);
        $c = $this->factory(10);
        self::assertTrue($a->equals($b));
        self::assertFalse($a->equals($c));
    }

    public function testIsBiggerThat(): void
    {
        $a = $this->factory(5);
        $b = $this->factory(1);
        $c = $this->factory(10);
        self::assertTrue($a->isBiggerThan($b));
        self::assertFalse($a->isBiggerThan($c));
    }

    public function testJson(): void
    {
        $val = $this->factory(5);
        self::assertSame('5', json_encode($val));
    }
}
