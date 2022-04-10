<?php

declare(strict_types=1);

namespace Warp\DataSource\Blame;

/**
 * @template T of object
 * @extends BlameImmutableInterface<T>
 */
interface BlameInterface extends BlameImmutableInterface
{
    /**
     * @param T|null $by
     */
    public function touch(?object $by = null): void;
}
