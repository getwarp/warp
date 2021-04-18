<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Bridge\LaminasHydrator;

use function class_alias;

class_alias(
    \spaceonfire\LaminasHydratorBridge\Strategy\BooleanStrategy::class,
    __NAMESPACE__ . '\BooleanStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\LaminasHydratorBridge\Strategy\BooleanStrategy instead.
     */
    class BooleanStrategy extends \spaceonfire\LaminasHydratorBridge\Strategy\BooleanStrategy
    {
    }
}
