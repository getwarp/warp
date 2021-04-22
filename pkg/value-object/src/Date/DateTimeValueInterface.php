<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

interface DateTimeValueInterface extends \DateTimeInterface, \Stringable, \JsonSerializable
{
    /**
     * @var int minute in seconds
     */
    public const MINUTE = 60;

    /**
     * @var int hour in seconds
     */
    public const HOUR = 60 * self::MINUTE;

    /**
     * @var int day in seconds
     */
    public const DAY = 24 * self::HOUR;

    /**
     * @var int week in seconds
     */
    public const WEEK = 7 * self::DAY;

    /**
     * @var int average month in seconds
     */
    public const MONTH = 2_629_800;

    /**
     * @var int average year in seconds
     */
    public const YEAR = 31_557_600;

    /**
     * DateTime object factory.
     * @param string|int|float|\DateTimeInterface|null $time
     * @return static
     */
    public static function from($time): self;

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
    ): self;

    /**
     * Returns new DateTime object formatted according to the specified format.
     * @param string $format The format the $time parameter should be in
     * @param string $time
     * @param \DateTimeZone|null $timezone (default timezone is used if null is passed)
     * @return static|null
     */
    public static function createFromFormat($format, $time, ?\DateTimeZone $timezone = null): ?self;
}
