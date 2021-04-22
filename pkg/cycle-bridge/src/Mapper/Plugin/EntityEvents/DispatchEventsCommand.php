<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin\EntityEvents;

use Psr\EventDispatcher\EventDispatcherInterface;
use spaceonfire\Bridge\Cycle\Mapper\AbstractCommand;

final class DispatchEventsCommand extends AbstractCommand
{
    /**
     * @var object[]
     */
    private array $events;

    private EventDispatcherInterface $dispatcher;

    /**
     * @param object[] $events
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(array $events, EventDispatcherInterface $dispatcher)
    {
        $this->events = $events;
        $this->dispatcher = $dispatcher;
    }

    public function isReady(): bool
    {
        return true;
    }

    public function complete(): void
    {
        foreach ($this->events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
