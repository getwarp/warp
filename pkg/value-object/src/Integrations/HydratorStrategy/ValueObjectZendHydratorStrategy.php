<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Integrations\HydratorStrategy;

use function class_alias;

class_alias(
    \spaceonfire\ValueObject\Bridge\LaminasHydrator\ValueObjectStrategy::class,
    __NAMESPACE__ . '\ValueObjectZendHydratorStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\ValueObject\Bridge\LaminasHydrator\ValueObjectStrategy instead.
     */
    class ValueObjectZendHydratorStrategy extends \spaceonfire\ValueObject\Bridge\LaminasHydrator\ValueObjectStrategy
    {
    }
}
