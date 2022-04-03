<?php

declare(strict_types=1);

namespace Warp\ValueObject\Bridge\LaminasHydrator;

use function class_alias;

class_alias(
    \Warp\LaminasHydratorBridge\Strategy\BooleanStrategy::class,
    __NAMESPACE__ . '\BooleanStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\LaminasHydratorBridge\Strategy\BooleanStrategy instead.
     */
    class BooleanStrategy extends \Warp\LaminasHydratorBridge\Strategy\BooleanStrategy
    {
    }
}
