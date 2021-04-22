<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin\Blame;

use spaceonfire\Bridge\Cycle\Mapper\Plugin\QueueBeforeEvent;
use spaceonfire\DataSource\Blame\BlamableInterface;
use spaceonfire\DataSource\Blame\BlameActorProviderInterface;

/**
 * @template T of object
 */
final class BlameHandler
{
    /**
     * @var BlameActorProviderInterface<T>
     */
    private BlameActorProviderInterface $actorProvider;

    private bool $force;

    /**
     * @param BlameActorProviderInterface<T> $actorProvider
     * @param bool $force
     */
    public function __construct(BlameActorProviderInterface $actorProvider, bool $force = false)
    {
        $this->actorProvider = $actorProvider;
        $this->force = $force;
    }

    public function handle(QueueBeforeEvent $event): void
    {
        $entity = $event->getEntity();

        if (!$entity instanceof BlamableInterface) {
            return;
        }

        $entity->blame($this->actorProvider->getActor(), $this->force);
    }
}
