<?php

declare(strict_types=1);

namespace Warp\ValueObject\Integrations\HydratorStrategy;

use function class_alias;

class_alias(
    \Warp\ValueObject\Bridge\LaminasHydrator\NullableStrategy::class,
    __NAMESPACE__ . '\NullableStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\ValueObject\Bridge\LaminasHydrator\NullableStrategy instead.
     */
    class NullableStrategy extends \Warp\ValueObject\Bridge\LaminasHydrator\NullableStrategy
    {
    }
}
