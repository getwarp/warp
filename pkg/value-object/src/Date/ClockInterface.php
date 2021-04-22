<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

interface ClockInterface
{
    public function now(): DateTimeImmutableValue;
}
