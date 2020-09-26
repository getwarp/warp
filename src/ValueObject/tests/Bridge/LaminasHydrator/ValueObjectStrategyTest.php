<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Bridge\LaminasHydrator;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use spaceonfire\ValueObject\IpValue;

class ValueObjectStrategyTest extends TestCase
{
    public function testConstruct(): void
    {
        new ValueObjectStrategy(IpValue::class);
        self::assertTrue(true);
    }

    public function testConstructFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ValueObjectStrategy(\stdClass::class);
    }

    public function testExtract(): void
    {
        $strategy = new ValueObjectStrategy(IpValue::class);

        $extracted = $strategy->extract(new IpValue('127.0.0.1'));
        self::assertSame('127.0.0.1', $extracted);
        self::assertSame('127.0.0.1', $strategy->extract('127.0.0.1'));
    }

    public function testHydrate(): void
    {
        $strategy = new ValueObjectStrategy(IpValue::class);

        $hydrated = $strategy->hydrate('127.0.0.1', null);
        self::assertSame('127.0.0.1', $hydrated->value());

        self::assertSame($hydrated, $strategy->hydrate($hydrated, null));
    }
}
