<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\UnionType;

final class UnionTypeFactory extends AbstractAggregatedTypeFactory
{
    protected const TYPE_CLASS = UnionType::class;
}
