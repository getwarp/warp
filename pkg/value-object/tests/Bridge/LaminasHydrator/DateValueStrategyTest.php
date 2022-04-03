<?php

declare(strict_types=1);

namespace Warp\ValueObject\Bridge\LaminasHydrator;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Warp\ValueObject\Date\DateTimeImmutableValue;

class DateValueStrategyTest extends TestCase
{
    public function testConstruct(): void
    {
        new DateValueStrategy('Y-m-d');
        self::assertTrue(true);
    }

    public function testConstructFailDateClassValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DateValueStrategy('Y-m-d', \DateTime::class);
    }

    public function testExtract(): void
    {
        $strategy = new DateValueStrategy('Y-m-d');

        $extracted = $strategy->extract(DateTimeImmutableValue::createFromFormat('Y-m-d', '2020-04-21'));
        self::assertSame('2020-04-21', $extracted);
    }

    public function testHydrate(): void
    {
        $strategy = new DateValueStrategy('Y-m-d');

        $date = DateTimeImmutableValue::createFromFormat('Y-m-d', '2020-04-21');
        $hydrated = $strategy->hydrate('2020-04-21', null);
        self::assertSame($date->getTimestamp(), $hydrated->getTimestamp());
    }

    public function testHydrateFallback(): void
    {
        $strategy = new DateValueStrategy('Y-m-d H:i:s.u');

        $date = DateTimeImmutableValue::createFromFormat('Y-m-d', '2020-04-21');
        $hydrated = $strategy->hydrate($date->format('Y-m-d H:i:s'), null);
        self::assertSame($date->getTimestamp(), $hydrated->getTimestamp());
    }

    public function testHydrateInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new DateValueStrategy('Y-m-d'))->hydrate(null);
    }
}
