<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

use DateTimeInterface;
use DateTimeZone;
use Exception;
use InvalidArgumentException;

trait DateTimeTrait
{
    /**
     * Custom DateTime constructor.
     * @param string $time
     * @param DateTimeZone|null $timezone
     */
    public function __construct($time = 'now', DateTimeZone $timezone = null)
    {
        try {
            parent::__construct($time, $timezone);
        } catch (Exception $exception) {
            throw new DateException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }

    /**
     * DateTime object factory.
     * @param string|int|DateTimeInterface $time
     * @return static
     */
    public static function from($time)
    {
        if ($time instanceof DateTimeInterface) {
            return new static($time->format('Y-m-d H:i:s.u'), $time->getTimezone());
        }

        if (is_numeric($time)) {
            if ($time <= DateTimeValueInterface::YEAR) {
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
            $hour < 0 || $hour > 23 ||
            $minute < 0 || $minute > 59 ||
            $second < 0 || $second >= 60 ||
            !checkdate($month, $day, $year)
        ) {
            throw new InvalidArgumentException("Invalid date '$s'");
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
        if ($timezone === null) {
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

    /**
     * Returns string representation
     * @return string
     */
    public function __toString(): string
    {
        return $this->format('Y-m-d H:i:s');
    }
}
