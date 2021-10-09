<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin\ForceEntityReference;

use spaceonfire\Bridge\Cycle\Mapper\Plugin\HydrateBeforeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
