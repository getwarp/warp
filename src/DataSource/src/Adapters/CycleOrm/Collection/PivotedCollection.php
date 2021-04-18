<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Collection;

use function class_alias;

class_alias(
    \spaceonfire\DataSource\Bridge\CycleOrm\Collection\PivotedCollection::class,
    __NAMESPACE__ . '\PivotedCollection'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\DataSource\Bridge\CycleOrm\Collection\PivotedCollection instead.
     */
    class PivotedCollection extends \spaceonfire\DataSource\Bridge\CycleOrm\Collection\PivotedCollection
    {
    }
}
