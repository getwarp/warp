<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Select;

use Cycle\ORM\Select;

interface PrepareSelectScopeInterface
{
    /**
     * @param Select<object> $select
     */
    public function prepareSelect(Select $select): void;
}
