<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

use PHPUnit\Framework\TestCase;

class FrozenClockTest extends TestCase
{
    public function testNowShouldReturnAlwaysTheSameObject(): void
    {
        $clock = new FrozenClock(SystemClock::fromUTC());

        $now = $clock->now();

        self::assertSame($now, $clock->now());
        self::assertSame($now, $clock->now());
    }

    public function testReset(): void
    {
        $clock = new FrozenClock(SystemClock::fromUTC());

        $oldNow = $clock->now();
        $clock->reset();
        $newNow = $clock->now();

        self::assertNotSame($oldNow, $clock->now());
        self::assertSame($newNow, $clock->now());
    }
}
