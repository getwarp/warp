<?php

declare(strict_types=1);

namespace Warp\ValueObject\Integrations\HydratorStrategy;

use function class_alias;

class_alias(
    \Warp\ValueObject\Bridge\LaminasHydrator\BooleanStrategy::class,
    __NAMESPACE__ . '\BooleanStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\ValueObject\Bridge\LaminasHydrator\BooleanStrategy instead.
     */
    class BooleanStrategy extends \Warp\ValueObject\Bridge\LaminasHydrator\BooleanStrategy
    {
    }
}
