<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use PHPUnit\Framework\TestCase;

class EnumValueTest extends TestCase
{
    /**
     * @param mixed $val
     * @return EnumValue
     */
    private function factory($val): EnumValue
    {
        /**
         * @method static self one()
         * @method static self twoWords()
         * @method static self manyManyWords()
         * @method static self oneString()
         */
        return new class($val) extends EnumValue {
            public const ONE = 1;
            public const TWO_WORDS = 2;
            public const MANY_MANY_WORDS = 3;
            public const ONE_STRING = '1';
            private const PRIVATE = 'private';
        };
    }

    public function testConstructor(): void
    {
        $val = $this->factory(1);
        self::assertSame(1, $val->value());
        self::assertSame('1', (string)$val);
        self::assertSame('"1"', json_encode($val));
    }

    public function testConstructorException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory(4);
    }

    public function testConstructFailWithObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory(new \stdClass());
    }

    public function testEquals(): void
    {
        $a = $this->factory(1);
        $b = $this->factory(1);
        $c = $this->factory(2);

        self::assertTrue($a->equals($b));
        self::assertFalse($a->equals($c));

        $b = $this->factory('1');
        self::assertFalse($a->equals($b));

        self::assertFalse($b->equals(new EmailValue('a@b.com')));
    }

    public function testRandom(): void
    {
        $enum = $this->factory(1);

        self::assertContains($enum::randomValue(), $enum::values());
        self::assertContains($enum::random()->value(), $enum::values());
    }

    public function testMagicCalls(): void
    {
        $enum = $this->factory(1);

        $a = $enum::one();
        $b = $enum::twoWords();

        self::assertInstanceOf(EnumValue::class, $a);
        self::assertInstanceOf(EnumValue::class, $b);

        self::assertSame(1, $a->value());
        self::assertSame(2, $b->value());
    }

    public function testPrivateConstantUnavailable(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory('private');
    }

    public function testValues(): void
    {
        $enum = $this->factory(1);
        self::assertSame([
            'one' => 1,
            'twoWords' => 2,
            'manyManyWords' => 3,
            'oneString' => '1',
        ], $enum::values());
    }
}
