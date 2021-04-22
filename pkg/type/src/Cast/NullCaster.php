<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

final class NullCaster implements CasterInterface
{
    public function accepts($value): bool
    {
        return true;
    }

    public function cast($value)
    {
        return null;
    }
}
