<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Integrations\HydratorStrategy;

use function class_alias;

class_alias(
    \spaceonfire\ValueObject\Bridge\LaminasHydrator\DateValueStrategy::class,
    __NAMESPACE__ . '\DateValueLaminasHydratorStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\ValueObject\Bridge\LaminasHydrator\DateValueStrategy instead.
     */
    class DateValueLaminasHydratorStrategy extends \spaceonfire\ValueObject\Bridge\LaminasHydrator\DateValueStrategy
    {
    }
}
