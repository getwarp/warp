<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

/**
 * @template E of object
 */
interface EntityReferenceInterface
{
    /**
     * @return E
     */
    public function getEntity(): object;
}
