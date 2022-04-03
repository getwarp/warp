<?php

declare(strict_types=1);

namespace Warp\ValueObject\Integrations\HydratorStrategy;

use function class_alias;

class_alias(
    \Warp\ValueObject\Bridge\LaminasHydrator\DateValueStrategy::class,
    __NAMESPACE__ . '\DateValueLaminasHydratorStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\ValueObject\Bridge\LaminasHydrator\DateValueStrategy instead.
     */
    class DateValueLaminasHydratorStrategy extends \Warp\ValueObject\Bridge\LaminasHydrator\DateValueStrategy
    {
    }
}
