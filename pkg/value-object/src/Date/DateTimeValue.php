<?php

declare(strict_types=1);

namespace Warp\ValueObject\Date;

use DateTime;

class DateTimeValue extends DateTime implements DateTimeValueInterface
{
    use DateTimeTrait;
}
