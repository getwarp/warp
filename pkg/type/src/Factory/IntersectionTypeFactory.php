<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\IntersectionType;

final class IntersectionTypeFactory extends AbstractAggregatedTypeFactory
{
    protected const TYPE_CLASS = IntersectionType::class;
}
