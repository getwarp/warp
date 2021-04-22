<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use PHPUnit\Framework\TestCase;

class IpValueTest extends TestCase
{
    public function testConstructor(): void
    {
        $val = IpValue::new('127.0.0.1');
        self::assertSame('127.0.0.1', $val->value());
    }

    public function testConstructorException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        IpValue::new('just string');
    }

    public function testConstructFailWithObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        IpValue::new(new \stdClass());
    }
}
