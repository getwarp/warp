<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Bridge\CycleOrm\Collection;

use spaceonfire\Collection\AbstractCollectionDecorator;

/**
 * Class PivotedCollection
 *
 * Attention: You should not extend this class because it will become final in the next major release
 * after the backward compatibility aliases are removed.
 *
 * @final
 */
class PivotedCollection extends AbstractCollectionDecorator implements PivotAwareInterface
{
    use PivotAwareTrait;
}
