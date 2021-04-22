<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\DisjunctionType;

final class DisjunctionTypeFactory extends AbstractAggregatedTypeFactory
{
    protected const TYPE_CLASS = DisjunctionType::class;
}
