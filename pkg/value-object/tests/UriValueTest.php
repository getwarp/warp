<?php

declare(strict_types=1);

namespace Warp\ValueObject;

use Http\Discovery\Psr17FactoryDiscovery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriValueTest extends TestCase
{
    public function testConstructor(): void
    {
        $val = UriValue::new('http://localhost');
        self::assertSame('http://localhost', (string)$val);
        self::assertSame('"http:\/\/localhost"', json_encode($val, JSON_THROW_ON_ERROR));
        self::assertSame('localhost', $val->value()->getHost());

        $val = UriValue::new($this->makeUri('http://localhost'));
        self::assertSame('http://localhost', (string)$val);
        self::assertSame('"http:\/\/localhost"', json_encode($val, JSON_THROW_ON_ERROR));
    }

    public function testConstructorException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        UriValue::new('http://:80');
    }

    public function testConstructFailWithObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        UriValue::new(new \stdClass());
    }

    private function makeUri($uri): UriInterface
    {
        return Psr17FactoryDiscovery::findUriFactory()->createUri($uri);
    }
}
