<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Blame;

/**
 * @template T of object
 */
interface BlamableInterface
{
    /**
     * @return BlameImmutableInterface<T>
     */
    public function getBlame(): BlameImmutableInterface;

    /**
     * @param T|null $by
     * @param bool $force
     */
    public function blame(?object $by, bool $force = false): void;
}
