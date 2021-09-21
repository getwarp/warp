<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Bridge\LaminasHydrator;

use function class_alias;

class_alias(
    \spaceonfire\LaminasHydratorBridge\Strategy\NullableStrategy::class,
    __NAMESPACE__ . '\NullableStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\LaminasHydratorBridge\Strategy\NullableStrategy instead.
     */
    class NullableStrategy extends \spaceonfire\LaminasHydratorBridge\Strategy\NullableStrategy
    {
    }
}
