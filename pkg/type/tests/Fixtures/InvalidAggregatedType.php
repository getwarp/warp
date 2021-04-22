<?php

declare(strict_types=1);

namespace spaceonfire\Type\Fixtures;

final class InvalidAggregatedType extends \spaceonfire\Type\AbstractAggregatedType
{
    public const DELIMITER = '';

    public function check($value): bool
    {
        return false;
    }
}
