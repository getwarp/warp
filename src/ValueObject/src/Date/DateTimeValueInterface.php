<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

use DateTimeInterface;
use JsonSerializable;

interface DateTimeValueInterface extends DateTimeInterface, JsonSerializable
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
    public const MONTH = 2629800;

    /**
     * @var int average year in seconds
     */
    public const YEAR = 31557600;
}
