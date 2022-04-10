<?php

declare(strict_types=1);

namespace Warp\Clock;

class DateTimeValue extends \DateTime implements DateTimeValueInterface
{
    use DateTimeTrait;
}
