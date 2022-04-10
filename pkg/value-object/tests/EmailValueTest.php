<?php

declare(strict_types=1);

namespace Warp\ValueObject;

use PHPUnit\Framework\TestCase;

class EmailValueTest extends TestCase
{
    public function testConstructor(): void
    {
        $val = EmailValue::new('test@test.test');
        self::assertSame('test@test.test', $val->value());
    }

    public function testConstructFail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        EmailValue::new('just string');
    }

    public function testConstructFailWithObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        EmailValue::new(new \stdClass());
    }
}
