<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Integrations\HydratorStrategy;

use function class_alias;

class_alias(
    \spaceonfire\ValueObject\Bridge\LaminasHydrator\NullableStrategy::class,
    __NAMESPACE__ . '\NullableStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\ValueObject\Bridge\LaminasHydrator\NullableStrategy instead.
     */
    class NullableStrategy extends \spaceonfire\ValueObject\Bridge\LaminasHydrator\NullableStrategy
    {
    }
}
