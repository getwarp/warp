<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

interface EntityEventsInterface
{
    /**
     * Releases accumulated events
     * @return object[]
     */
    public function releaseEvents(): array;
}
