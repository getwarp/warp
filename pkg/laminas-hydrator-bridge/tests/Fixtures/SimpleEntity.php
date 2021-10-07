<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\LaminasHydrator\Fixtures;

final class SimpleEntity
{
    public int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }
}
