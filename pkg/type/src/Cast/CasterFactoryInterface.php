<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

use spaceonfire\Type\TypeInterface;

interface CasterFactoryInterface
{
    public function make(TypeInterface $type): ?CasterInterface;
}
