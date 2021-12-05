<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

/**
 * Trait DateTimeTrait
 *
 * Inspired by `nette/utils` DateTime
 * @see https://github.com/nette/utils/blob/master/src/Utils/DateTime.php
 */
trait DateTimeTrait
{
    /**
     * @param string $time
     * @param \DateTimeZone|null $timezone
     */
    final public function __construct($time = 'now', ?\DateTimeZone $timezone = null)
    {
        try {
            parent::__construct($time, $timezone);
        } catch (\Throwable $exception) {
            throw new DateException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    public function __toString(): string
    {
        return $this->format('Y-m-d H:i:s');
    }

    public static function from($time): self
    {
        if ($time instanceof static) {
            return $time;
        }

        if ($time instanceof \DateTimeInterface) {
            return new static($time->format('Y-m-d H:i:s.u'), $time->getTimezone());
        }

        if (\is_numeric($time)) {
            $time = (int)$time;
            if (DateTimeValueInterface::YEAR >= $time) {
                $time += \time();
            }
            return (new static('@' . $time))->setTimezone(new \DateTimeZone(\date_default_timezone_get()));
        }

        // textual or null
        return new static($time ?? 'now');
    }

    public static function fromParts(
        int $year,
        int $month,
        int $day,
        int $hour = 0,
        int $minute = 0,
        float $second = 0.0
    ): self {
        $s = \sprintf('%04d-%02d-%02d %02d:%02d:%02.5f', $year, $month, $day, $hour, $minute, $second);

        if (
            0 > $hour || 23 < $hour ||
            0 > $minute || 59 < $minute ||
            0 > $second || 60 <= $second ||
            !\checkdate($month, $day, $year)
        ) {
            throw new \InvalidArgumentException(\sprintf('Invalid date: "%s".', $s));
        }

        return new static($s);
    }

    #[\ReturnTypeWillChange]
    public static function createFromFormat($format, $time, ?\DateTimeZone $timezone = null): ?self
    {
        $date = parent::createFromFormat($format, $time, $timezone ?? new \DateTimeZone(\date_default_timezone_get()));
        return $date ? static::from($date) : null;
    }

    /**
     * Returns JSON representation in ISO 8601 (used by JavaScript).
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->format('c');
    }
}
