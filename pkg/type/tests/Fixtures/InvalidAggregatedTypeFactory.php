<?php

declare(strict_types=1);

namespace Warp\Type\Fixtures;

final class InvalidAggregatedTypeFactory extends \Warp\Type\Factory\AbstractAggregatedTypeFactory
{
    protected const TYPE_CLASS = InvalidAggregatedType::class;
}
