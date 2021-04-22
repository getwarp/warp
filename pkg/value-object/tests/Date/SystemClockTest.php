<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

use PHPUnit\Framework\TestCase;

class SystemClockTest extends TestCase
{
    public function testConstructUsingTimezone(): void
    {
        $timezone = new \DateTimeZone('+0400');
        $clock = new SystemClock($timezone);

        $lower = new DateTimeImmutableValue('now', $timezone);
        $now = $clock->now();
        $upper = new DateTimeImmutableValue('now', $timezone);

        self::assertEquals($timezone, $now->getTimezone());
        self::assertGreaterThanOrEqual($lower, $now);
        self::assertLessThanOrEqual($upper, $now);
    }

    public function testFromUTC(): void
    {
        $clock = SystemClock::fromUTC();
        $now = $clock->now();

        self::assertSame('UTC', $now->getTimezone()->getName());
    }

    public function testFromSystemTimezone(): void
    {
        $clock = SystemClock::fromSystemTimezone();
        $now = $clock->now();

        self::assertSame(\date_default_timezone_get(), $now->getTimezone()->getName());
    }
}
