<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator\Strategy;

use PHPUnit\Framework\TestCase;
use Warp\ValueObject\IpValue;

class ValueObjectStrategyTest extends TestCase
{
    public function testDefault(): void
    {
        $strategy = new ValueObjectStrategy(IpValue::class);

        self::assertSame('127.0.0.1', $strategy->extract(IpValue::new('127.0.0.1')));
        self::assertSame('127.0.0.1', $strategy->extract('127.0.0.1'));

        $hydrated = $strategy->hydrate('127.0.0.1');
        self::assertSame('127.0.0.1', $hydrated->value());
        self::assertSame($hydrated, $strategy->hydrate($hydrated));
    }

    public function testFailConstructWithInvalidValueObjectClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ValueObjectStrategy(\stdClass::class);
    }
}
