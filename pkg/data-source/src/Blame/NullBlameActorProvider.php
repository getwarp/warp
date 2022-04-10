<?php

declare(strict_types=1);

namespace Warp\DataSource\Blame;

/**
 * @implements BlameActorProviderInterface<object>
 */
class NullBlameActorProvider implements BlameActorProviderInterface
{
    public function getActor(): ?object
    {
        return null;
    }
}
