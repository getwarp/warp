<?php

declare(strict_types=1);

namespace Warp\ValueObject;

use PHPUnit\Framework\TestCase;
use Warp\ValueObject\Fixtures\FixtureInt;

class IntValueTest extends TestCase
{
    private function factory($val): AbstractIntValue
    {
        return FixtureInt::new($val);
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

    public function testComparison(): void
    {
        $one = $this->factory(1);
        $five = $this->factory(5);
        $ten = $this->factory(10);

        self::assertTrue($one->lt($five));
        self::assertFalse($one->lt($one));
        self::assertTrue($one->lte($one));

        self::assertTrue($five->gt($one));
        self::assertTrue($five->gte($one));
        self::assertFalse($five->gt($ten));
    }

    public function testJson(): void
    {
        $val = $this->factory(5);
        self::assertSame('5', json_encode($val, JSON_THROW_ON_ERROR));
    }
}
