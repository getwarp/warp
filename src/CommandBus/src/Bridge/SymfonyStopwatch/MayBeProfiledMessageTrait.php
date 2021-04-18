<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Bridge\SymfonyStopwatch;

trait MayBeProfiledMessageTrait
{
    /**
     * @var string|null
     */
    protected $profilingEventName;
    /**
     * @var string|null
     */
    protected $profilingCategory;

    /**
     * Returns event name for profiling
     * @return string
     */
    public function getProfilingEventName(): ?string
    {
        return $this->profilingEventName;
    }

    /**
     * Returns event category for profiling
     * @return string|null
     */
    public function getProfilingCategory(): ?string
    {
        return $this->profilingCategory;
    }

    /**
     * Setter for `profilingEventName` property
     * @param string|null $profilingEventName
     * @return static
     */
    public function setProfilingEventName(?string $profilingEventName): self
    {
        $this->profilingEventName = $profilingEventName;
        return $this;
    }

    /**
     * Setter for `profilingCategory` property
     * @param string|null $profilingCategory
     * @return static
     */
    public function setProfilingCategory(?string $profilingCategory): self
    {
        $this->profilingCategory = $profilingCategory;
        return $this;
    }
}
