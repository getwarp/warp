<?php

declare(strict_types=1);

namespace Warp\Clock;

interface ClockInterface
{
    public function now(): DateTimeImmutableValue;
}
