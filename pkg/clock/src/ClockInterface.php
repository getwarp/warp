<?php

declare(strict_types=1);

namespace Warp\Clock;

interface ClockInterface extends \Psr\Clock\ClockInterface
{
    public function now(): DateTimeImmutableValue;
}
