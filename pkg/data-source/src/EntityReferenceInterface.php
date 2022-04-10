<?php

declare(strict_types=1);

namespace Warp\DataSource;

/**
 * @template E of object
 */
interface EntityReferenceInterface
{
    /**
     * @return E
     */
    public function getEntity(): object;

    /**
     * @return E|null
     */
    public function getEntityOrNull(): ?object;

    /**
     * @param self<E> $other
     * @return bool
     */
    public function equals(self $other): bool;
}
