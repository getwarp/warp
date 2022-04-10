<?php

declare(strict_types=1);

namespace Warp\DataSource\Blame;

/**
 * @template T of object
 */
interface BlameActorProviderInterface
{
    /**
     * @return T|null
     */
    public function getActor(): ?object;
}
