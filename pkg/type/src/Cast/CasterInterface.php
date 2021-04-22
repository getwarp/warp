<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

interface CasterInterface
{
    /**
     * @param mixed $value
     * @return bool
     */
    public function accepts($value): bool;

    /**
     * @param mixed $value
     * @return mixed
     */
    public function cast($value);
}
