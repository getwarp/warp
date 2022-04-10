<?php

declare(strict_types=1);

namespace Warp\Exception;

interface MetadataEnabledInterface
{
    /**
     * @return array<string,mixed>
     */
    public function getMetadata(): array;
}
