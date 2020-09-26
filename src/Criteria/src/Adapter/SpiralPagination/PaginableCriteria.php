<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Adapter\SpiralPagination;

use function class_alias;

class_alias(
    \spaceonfire\Criteria\Bridge\SpiralPagination\PaginableCriteria::class,
    __NAMESPACE__ . '\PaginableCriteria'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\Criteria\Bridge\SpiralPagination\PaginableCriteria instead.
     */
    class PaginableCriteria extends \spaceonfire\Criteria\Bridge\SpiralPagination\PaginableCriteria
    {
    }
}
