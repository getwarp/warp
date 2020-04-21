<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Integrations\HydratorStrategy;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use spaceonfire\ValueObject\Date\DateTimeImmutableValue;

class DateValueLaminasHydratorStrategyTest extends TestCase
{
    public function testConstruct(): void
    {
        new DateValueLaminasHydratorStrategy('Y-m-d');
        self::assertTrue(true);
    }

    public function testConstructFailDateClassValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DateValueLaminasHydratorStrategy('Y-m-d', \DateTime::class);
    }

    public function testExtract(): void
    {
        $strategy = new DateValueLaminasHydratorStrategy('Y-m-d');

        $extracted = $strategy->extract(DateTimeImmutableValue::createFromFormat('Y-m-d', '2020-04-21'));
        self::assertEquals('2020-04-21', $extracted);
    }

    public function testHydrate(): void
    {
        $strategy = new DateValueLaminasHydratorStrategy('Y-m-d');

        $date = DateTimeImmutableValue::createFromFormat('Y-m-d', '2020-04-21');
        $hydrated = $strategy->hydrate('2020-04-21', null);
        self::assertEquals($date->getTimestamp(), $hydrated->getTimestamp());
    }
}
