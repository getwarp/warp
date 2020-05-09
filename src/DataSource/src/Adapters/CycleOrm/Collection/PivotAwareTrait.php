<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Collection;

use RuntimeException;
use SplObjectStorage;

trait PivotAwareTrait
{
    /**
     * @var SplObjectStorage
     */
    protected $pivotContext;

    /**
     * Get associated pivot data.
     *
     * @return SplObjectStorage
     */
    public function getPivotContext(): SplObjectStorage
    {
        if ($this->pivotContext === null) {
            throw new RuntimeException('Pivot context not defined');
        }

        return $this->pivotContext;
    }

    /**
     * Set associated pivot data.
     *
     * @param SplObjectStorage $pivotContext
     * @return mixed|void
     */
    public function setPivotContext(SplObjectStorage $pivotContext)
    {
        $this->pivotContext = $pivotContext;
    }
}
