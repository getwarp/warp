<?php

declare(strict_types=1);

namespace spaceonfire\Clock;

interface ClockInterface
{
    public function now(): DateTimeImmutableValue;
}
