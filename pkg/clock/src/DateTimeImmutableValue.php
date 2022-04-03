<?php

declare(strict_types=1);

namespace spaceonfire\Clock;

class DateTimeImmutableValue extends \DateTimeImmutable implements DateTimeValueInterface
{
    use DateTimeTrait;
}
