<?php

declare(strict_types=1);

namespace Warp\ValueObject\Integrations\HydratorStrategy;

use function class_alias;

class_alias(
    \Warp\ValueObject\Bridge\LaminasHydrator\ValueObjectStrategy::class,
    __NAMESPACE__ . '\ValueObjectLaminasHydratorStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\ValueObject\Bridge\LaminasHydrator\ValueObjectStrategy instead.
     */
    class ValueObjectLaminasHydratorStrategy extends \Warp\ValueObject\Bridge\LaminasHydrator\ValueObjectStrategy
    {
    }
}
