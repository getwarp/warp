<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator\Strategy;

use PHPUnit\Framework\TestCase;
use Warp\Clock\DateTimeImmutableValue;
use Warp\Clock\DateTimeValue;

class DateValueStrategyTest extends TestCase
{
    public function testDefault(): void
    {
        $strategy = new DateValueStrategy('Y-m-d');

        $date = DateTimeImmutableValue::createFromFormat('Y-m-d', '2020-04-21');

        $extracted = $strategy->extract($date);

        self::assertSame('2020-04-21', $extracted);

        $hydrated = $strategy->hydrate($extracted);

        self::assertInstanceOf(DateTimeImmutableValue::class, $hydrated);
        self::assertSame($date->getTimestamp(), $hydrated->getTimestamp());
    }

    public function testMutable(): void
    {
        $strategy = new DateValueStrategy('Y-m-d', DateTimeValue::class);
        $hydrated = $strategy->hydrate('2020-04-21');
        self::assertInstanceOf(DateTimeValue::class, $hydrated);
    }

    public function testHydrateFallback(): void
    {
        $strategy = new DateValueStrategy('Y-m-d H:i:s.u');

        $hydrated = $strategy->hydrate('2020-04-21');
        self::assertSame('2020-04-21 00:00:00.000000', $strategy->extract($hydrated));
    }

    public function testConstructFailDateClassValidation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DateValueStrategy('Y-m-d', \DateTime::class);
    }

    public function testExtractInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new DateValueStrategy('Y-m-d'))->extract('2020-04-21');
    }

    public function testHydrateInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new DateValueStrategy('Y-m-d'))->hydrate(null);
    }

    public function testHydrateRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);

        (new DateValueStrategy('Y-m-d'))->hydrate('-');
    }

    public function testHydrateDateTimeInstance(): void
    {
        $system = new \DateTimeImmutable();
        $warp = DateTimeImmutableValue::from($system);

        $strategy = new DateValueStrategy('Y-m-d');

        self::assertEquals($system, $strategy->hydrate($system));
        self::assertSame($warp, $strategy->hydrate($warp));
    }

    public function testTimezoneSupport(): void
    {
        $utc = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $winnipeg = $utc->setTimezone(new \DateTimeZone('America/Winnipeg'));

        $strategy = new DateValueStrategy('Y-m-d H:i:s.u', DateTimeImmutableValue::class, new \DateTimeZone('UTC'));

        $hydrateResult = $strategy->hydrate($winnipeg);
        self::assertEquals($utc, $hydrateResult);
        self::assertSame($utc->format('Y-m-d H:i:s.u'), $strategy->extract(DateTimeImmutableValue::from($winnipeg)));
    }
}
