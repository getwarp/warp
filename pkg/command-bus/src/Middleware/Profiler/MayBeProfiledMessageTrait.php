<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Middleware\Profiler;

trait MayBeProfiledMessageTrait
{
    protected ?string $profilingEventName = null;

    protected ?string $profilingCategory = null;

    public function getProfilingEventName(): ?string
    {
        return $this->profilingEventName;
    }

    public function getProfilingCategory(): ?string
    {
        return $this->profilingCategory;
    }

    public function setProfilingEventName(?string $profilingEventName): void
    {
        $this->profilingEventName = $profilingEventName;
    }

    public function setProfilingCategory(?string $profilingCategory): void
    {
        $this->profilingCategory = $profilingCategory;
    }
}
