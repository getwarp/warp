<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Integrations\HydratorStrategy;

use function class_alias;

class_alias(
    \spaceonfire\ValueObject\Bridge\LaminasHydrator\BooleanStrategy::class,
    __NAMESPACE__ . '\BooleanStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\ValueObject\Bridge\LaminasHydrator\BooleanStrategy instead.
     */
    class BooleanStrategy extends \spaceonfire\ValueObject\Bridge\LaminasHydrator\BooleanStrategy
    {
    }
}
