<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin\EntityEvents;

use spaceonfire\Bridge\Cycle\Mapper\Plugin\QueueAfterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class EntityEventsPlugin implements EventSubscriberInterface
{
    private EntityEventsHandler $handler;

    public function __construct(EntityEventsHandler $handler)
    {
        $this->handler = $handler;
    }

    public function handle(QueueAfterEvent $event): void
    {
        $this->handler->handle($event);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            QueueAfterEvent::class => 'handle',
        ];
    }
}
