<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

final class FrozenClock implements ClockInterface
{
    private ClockInterface $clock;

    private DateTimeImmutableValue $now;

    public function __construct(ClockInterface $clock)
    {
        $this->clock = $clock;
        $this->now = $clock->now();
    }

    public function reset(): void
    {
        $this->now = $this->clock->now();
    }

    public function now(): DateTimeImmutableValue
    {
        return $this->now;
    }
}
