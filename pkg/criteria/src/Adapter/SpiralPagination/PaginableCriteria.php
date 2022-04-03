<?php

declare(strict_types=1);

namespace Warp\Criteria\Adapter\SpiralPagination;

use function class_alias;

class_alias(
    \Warp\Criteria\Bridge\SpiralPagination\PaginableCriteria::class,
    __NAMESPACE__ . '\PaginableCriteria'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\Criteria\Bridge\SpiralPagination\PaginableCriteria instead.
     */
    class PaginableCriteria extends \Warp\Criteria\Bridge\SpiralPagination\PaginableCriteria
    {
    }
}
