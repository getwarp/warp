<?php

declare(strict_types=1);

namespace spaceonfire\Clock;

final class InstantClock implements ClockInterface
{
    private DateTimeImmutableValue $now;

    public function __construct(DateTimeImmutableValue $now)
    {
        $this->now = $now;
    }

    public function now(): DateTimeImmutableValue
    {
        return $this->now;
    }
}
