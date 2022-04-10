<?php

declare(strict_types=1);

namespace Warp\Clock;

class DateTimeImmutableValue extends \DateTimeImmutable implements DateTimeValueInterface
{
    use DateTimeTrait;
}
