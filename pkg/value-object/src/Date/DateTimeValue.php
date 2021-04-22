<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

class DateTimeValue extends \DateTime implements DateTimeValueInterface
{
    use DateTimeTrait;
}
