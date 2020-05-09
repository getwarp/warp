<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Collection;

use spaceonfire\Collection\BaseCollection;

class PivotedCollection extends BaseCollection implements PivotAwareInterface
{
    use PivotAwareTrait;
}
