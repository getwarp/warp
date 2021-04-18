<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Mapper\Hydrator\Strategy;

use function class_alias;

class_alias(
    \spaceonfire\DataSource\Bridge\CycleOrm\Mapper\Hydrator\Strategy\CollectionExtractorStrategy::class,
    __NAMESPACE__ . '\CollectionExtractorStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\DataSource\Bridge\CycleOrm\Mapper\Hydrator\Strategy\CollectionExtractorStrategy instead.
     */
    class CollectionExtractorStrategy extends \spaceonfire\DataSource\Bridge\CycleOrm\Mapper\Hydrator\Strategy\CollectionExtractorStrategy
    {
    }
}
