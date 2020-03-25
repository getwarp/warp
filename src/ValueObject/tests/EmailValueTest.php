<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use PHPUnit\Framework\TestCase;

class EmailValueTest extends TestCase
{
    public function testConstructor()
    {
        $val = new EmailValue('test@test.test');
        $this->assertEquals('test@test.test', $val->value());
    }

    public function testConstructorException()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EmailValue('just string');
    }
}
