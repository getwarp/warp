<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Collection;

use spaceonfire\Collection\AbstractCollectionDecorator;

class PivotedCollection extends AbstractCollectionDecorator implements PivotAwareInterface
{
    use PivotAwareTrait;
}
