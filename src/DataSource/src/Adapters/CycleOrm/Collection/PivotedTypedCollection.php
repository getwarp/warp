<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Collection;

use spaceonfire\Collection\TypedCollection;

class PivotedTypedCollection extends TypedCollection implements PivotAwareInterface
{
    use PivotAwareTrait;
}
