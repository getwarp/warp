<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use Throwable;

/**
 * Trait DateTimeTrait
 *
 * Inspired by `nette/utils` DateTime
 * @see https://github.com/nette/utils/blob/master/src/Utils/DateTime.php
 */
trait DateTimeTrait
{
    /**
     * Custom DateTime constructor.
     * @param string $time
     * @param DateTimeZone|null $timezone
     */
    final public function __construct($time = 'now', ?DateTimeZone $timezone = null)
    {
        try {
            parent::__construct($time, $timezone);
        } catch (Throwable $exception) {
            throw new DateException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    /**
     * Returns string representation
     * @return string
     */
    public function __toString(): string
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * DateTime object factory.
     * @param string|int|float|DateTimeInterface|null $time
     * @return static
     */
    public static function from($time)
    {
        if ($time instanceof DateTimeInterface) {
            return new static($time->format('Y-m-d H:i:s.u'), $time->getTimezone());
        }

        if (is_numeric($time)) {
            $time = (int)$time;
            if (DateTimeValueInterface::YEAR >= $time) {
                $time += time();
            }
            return (new static('@' . $time))->setTimezone(new DateTimeZone(date_default_timezone_get()));
        }

        // textual or null
        return new static($time ?? 'now');
    }

    /**
     * Creates DateTime object.
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param float $second
     * @return static
     */
    public static function fromParts(
        int $year,
        int $month,
        int $day,
        int $hour = 0,
        int $minute = 0,
        float $second = 0.0
    ) {
        $s = sprintf('%04d-%02d-%02d %02d:%02d:%02.5f', $year, $month, $day, $hour, $minute, $second);
        if (
            0 > $hour || 23 < $hour ||
            0 > $minute || 59 < $minute ||
            0 > $second || 60 <= $second ||
            !checkdate($month, $day, $year)
        ) {
            throw new InvalidArgumentException("Invalid date '${s}'");
        }
        return new static($s);
    }

    /**
     * Returns new DateTime object formatted according to the specified format.
     * @param string $format The format the $time parameter should be in
     * @param string $time
     * @param string|DateTimeZone $timezone (default timezone is used if null is passed)
     * @return static|null
     */
    public static function createFromFormat($format, $time, $timezone = null)
    {
        if (null === $timezone) {
            $timezone = new DateTimeZone(date_default_timezone_get());
        } elseif (is_string($timezone)) {
            $timezone = new DateTimeZone($timezone);
        } elseif (!$timezone instanceof DateTimeZone) {
            throw new InvalidArgumentException('Invalid timezone given');
        }

        $date = parent::createFromFormat($format, $time, $timezone);
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
