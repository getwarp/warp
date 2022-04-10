<?php

declare(strict_types=1);

namespace Warp\Container\Fixtures;

function intval($value, int $base = 10): int
{
    return \intval($value, $base);
}
