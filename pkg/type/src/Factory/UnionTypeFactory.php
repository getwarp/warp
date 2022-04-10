<?php

declare(strict_types=1);

namespace Warp\Type\Factory;

use Warp\Type\UnionType;

final class UnionTypeFactory extends AbstractAggregatedTypeFactory
{
    protected const TYPE_CLASS = UnionType::class;
}
