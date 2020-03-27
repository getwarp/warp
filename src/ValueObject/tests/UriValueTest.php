<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriValueTest extends TestCase
{
    public function testConstructor()
    {
        $val = new UriValue('http://localhost');
        $this->assertInstanceOf(UriInterface::class, $val->value());
        $this->assertEquals('http://localhost', (string)$val);
        $this->assertEquals('"http:\/\/localhost"', json_encode($val));

        $val = new UriValue(new Uri('http://localhost'));
        $this->assertInstanceOf(UriInterface::class, $val->value());
        $this->assertEquals('http://localhost', (string)$val);
        $this->assertEquals('"http:\/\/localhost"', json_encode($val));
    }

    public function testConstructorException()
    {
        $this->expectException(\InvalidArgumentException::class);
        new UriValue('http://:80');
    }

    public function testConstructFailWithObject()
    {
        $this->expectException(\InvalidArgumentException::class);
        new UriValue(new \stdClass());
    }
}
