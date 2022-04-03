<?php

declare(strict_types=1);

namespace Warp\DataSource\Adapters\CycleOrm\Collection;

use function class_alias;

class_alias(
    \Warp\DataSource\Bridge\CycleOrm\Collection\PivotedCollection::class,
    __NAMESPACE__ . '\PivotedCollection'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\DataSource\Bridge\CycleOrm\Collection\PivotedCollection instead.
     */
    class PivotedCollection extends \Warp\DataSource\Bridge\CycleOrm\Collection\PivotedCollection
    {
    }
}
