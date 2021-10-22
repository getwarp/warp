<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection;

/**
 * @template T of object
 * @template P
 */
interface ChangesEnabledInterface
{
    public function hasChanges(): bool;

    /**
     * @return array<Change<T,P>>
     */
    public function releaseChanges(): array;
}
