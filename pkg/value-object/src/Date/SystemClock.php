<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

final class SystemClock implements ClockInterface
{
    private \DateTimeZone $timezone;

    public function __construct(\DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    public static function fromUTC(): self
    {
        return new self(new \DateTimeZone('UTC'));
    }

    public static function fromSystemTimezone(): self
    {
        return new self(new \DateTimeZone(\date_default_timezone_get()));
    }

    public function now(): DateTimeImmutableValue
    {
        return new DateTimeImmutableValue('now', $this->timezone);
    }
}
