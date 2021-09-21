<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Bridge\SymfonyStopwatch;

interface MayBeProfiledMessageInterface
{
    /**
     * Returns event name for profiling
     * @return string|null
     */
    public function getProfilingEventName(): ?string;

    /**
     * Returns event category for profiling
     * @return string|null
     */
    public function getProfilingCategory(): ?string;
}
