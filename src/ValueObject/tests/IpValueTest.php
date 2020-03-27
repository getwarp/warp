<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use PHPUnit\Framework\TestCase;

class IpValueTest extends TestCase
{
    public function testConstructor()
    {
        $val = new IpValue('127.0.0.1');
        $this->assertEquals('127.0.0.1', $val->value());
    }

    public function testConstructorException()
    {
        $this->expectException(\InvalidArgumentException::class);
        new IpValue('just string');
    }

    public function testConstructFailWithObject()
    {
        $this->expectException(\InvalidArgumentException::class);
        new IpValue(new \stdClass());
    }
}
