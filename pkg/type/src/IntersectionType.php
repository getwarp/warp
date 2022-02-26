<?php

declare(strict_types=1);

namespace spaceonfire\Type;

final class IntersectionType extends AbstractAggregatedType
{
    public const DELIMITER = '&';

    public function check($value): bool
    {
        foreach ($this->types as $type) {
            if (!$type->check($value)) {
                return false;
            }
        }

        return true;
    }
}
