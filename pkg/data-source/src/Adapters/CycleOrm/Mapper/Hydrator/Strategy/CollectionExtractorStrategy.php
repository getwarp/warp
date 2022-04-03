<?php

declare(strict_types=1);

namespace Warp\DataSource\Adapters\CycleOrm\Mapper\Hydrator\Strategy;

use function class_alias;

class_alias(
    \Warp\DataSource\Bridge\CycleOrm\Mapper\Hydrator\Strategy\CollectionExtractorStrategy::class,
    __NAMESPACE__ . '\CollectionExtractorStrategy'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\DataSource\Bridge\CycleOrm\Mapper\Hydrator\Strategy\CollectionExtractorStrategy instead.
     */
    class CollectionExtractorStrategy extends \Warp\DataSource\Bridge\CycleOrm\Mapper\Hydrator\Strategy\CollectionExtractorStrategy
    {
    }
}
