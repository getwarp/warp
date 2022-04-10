<?php

declare(strict_types=1);

namespace Warp\ValueObject;

use PHPUnit\Framework\TestCase;
use Warp\ValueObject\Fixtures\FixtureEnum;

class EnumValueTest extends TestCase
{
    /**
     * @param mixed $val
     * @return AbstractEnumValue
     */
    private function factory($val): AbstractEnumValue
    {
        return FixtureEnum::new($val);
    }

    public function testConstructor(): void
    {
        $val = $this->factory(1);
        self::assertSame(1, $val->value());
        self::assertSame('1', (string)$val);
        self::assertSame('1', json_encode($val, JSON_THROW_ON_ERROR));
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

        self::assertFalse($b->equals(EmailValue::new('a@b.com')));
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

        self::assertInstanceOf(AbstractEnumValue::class, $a);
        self::assertInstanceOf(AbstractEnumValue::class, $b);

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

    public function testValuesInvalid(): void
    {
        $this->expectException(\LogicException::class);

        Fixtures\InvalidEnum::values();
    }
}
