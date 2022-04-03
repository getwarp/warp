<?php

declare(strict_types=1);

namespace Warp\ValueObject\Bridge\LaminasHydrator;

use function class_alias;

class_alias(
    \Warp\LaminasHydratorBridge\Strategy\NullableStrategy::class,
    __NAMESPACE__ . '\NullableStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\LaminasHydratorBridge\Strategy\NullableStrategy instead.
     */
    class NullableStrategy extends \Warp\LaminasHydratorBridge\Strategy\NullableStrategy
    {
    }
}
