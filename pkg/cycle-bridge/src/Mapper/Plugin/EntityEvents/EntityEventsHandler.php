<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Mapper\Plugin\EntityEvents;

use Psr\EventDispatcher\EventDispatcherInterface;
use Warp\Bridge\Cycle\Mapper\Plugin\QueueAfterEvent;
use Warp\DataSource\EntityEventsInterface;

final class EntityEventsHandler
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(QueueAfterEvent $event): void
    {
        $entity = $event->getEntity();

        if (!$entity instanceof EntityEventsInterface) {
            return;
        }

        $events = $entity->releaseEvents();
        if (0 === \count($events)) {
            return;
        }

        $command = $event->makeSequence($event->getCommand());
        $command->addCommand(new DispatchEventsCommand($events, $this->dispatcher));

        $event->replaceCommand($command);
    }
}
