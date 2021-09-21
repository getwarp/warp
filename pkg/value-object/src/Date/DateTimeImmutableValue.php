<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

use DateTimeImmutable;

class DateTimeImmutableValue extends DateTimeImmutable implements DateTimeValueInterface
{
    use DateTimeTrait;
}
