<?php

declare(strict_types=1);

namespace spaceonfire\Type;

final class DisjunctionType extends AbstractAggregatedType
{
    public const DELIMITER = '|';

    public function check($value): bool
    {
        foreach ($this->types as $type) {
            if ($type->check($value)) {
                return true;
            }
        }

        return false;
    }
}
