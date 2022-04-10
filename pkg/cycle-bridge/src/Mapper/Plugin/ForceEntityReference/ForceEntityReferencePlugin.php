<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Mapper\Plugin\ForceEntityReference;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Warp\Bridge\Cycle\Mapper\Plugin\HydrateBeforeEvent;

final class ForceEntityReferencePlugin implements EventSubscriberInterface
{
    private ForceEntityReferenceHandler $handler;

    public function __construct(ForceEntityReferenceHandler $handler)
    {
        $this->handler = $handler;
    }

    public function onHydrate(HydrateBeforeEvent $event): void
    {
        $this->handler->onHydrate($event);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HydrateBeforeEvent::class => 'onHydrate',
        ];
    }
}
