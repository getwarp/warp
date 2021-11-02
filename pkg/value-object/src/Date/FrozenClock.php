<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

final class FrozenClock implements ClockInterface
{
    private ClockInterface $clock;

    private ?DateTimeImmutableValue $now = null;

    public function __construct(ClockInterface $clock)
    {
        $this->clock = $clock;
    }

    public function reset(): void
    {
        $this->now = null;
    }

    public function now(): DateTimeImmutableValue
    {
        return $this->now ??= $this->clock->now();
    }
}
