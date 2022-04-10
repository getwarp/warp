<?php

declare(strict_types=1);

namespace Warp\Type\Factory;

use Warp\Type\IntersectionType;

final class IntersectionTypeFactory extends AbstractAggregatedTypeFactory
{
    protected const TYPE_CLASS = IntersectionType::class;
}
