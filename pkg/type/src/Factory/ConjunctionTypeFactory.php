<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\ConjunctionType;

final class ConjunctionTypeFactory extends AbstractAggregatedTypeFactory
{
    protected const TYPE_CLASS = ConjunctionType::class;
}
