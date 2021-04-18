<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use PHPUnit\Framework\TestCase;

class EmailValueTest extends TestCase
{
    public function testConstructor(): void
    {
        $val = new EmailValue('test@test.test');
        self::assertSame('test@test.test', $val->value());
    }

    public function testConstructFail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new EmailValue('just string');
    }

    public function testConstructFailWithObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new EmailValue(new \stdClass());
    }
}
