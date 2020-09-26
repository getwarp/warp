<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Bridge\CycleOrm\Collection;

use spaceonfire\Collection\CollectionInterface;
use SplObjectStorage;

/**
 * Carries pivot data associated with each element.
 */
interface PivotAwareInterface extends CollectionInterface
{
    /**
     * Get associated pivot data.
     *
     * @return SplObjectStorage
     */
    public function getPivotContext(): SplObjectStorage;

    /**
     * Set associated pivot data.
     *
     * @param SplObjectStorage $pivotContext
     * @return mixed|void
     */
    public function setPivotContext(SplObjectStorage $pivotContext);
}
