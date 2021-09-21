<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

use DateTime;

class DateTimeValue extends DateTime implements DateTimeValueInterface
{
    use DateTimeTrait;
}
