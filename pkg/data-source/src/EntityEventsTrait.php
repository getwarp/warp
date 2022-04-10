<?php

declare(strict_types=1);

namespace Warp\DataSource;

trait EntityEventsTrait
{
    /**
     * @var object[]
     */
    private array $events = [];

    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    final protected function recordEvent(object $event): void
    {
        $this->events[] = $event;
    }
}
