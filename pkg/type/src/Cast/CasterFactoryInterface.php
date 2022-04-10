<?php

declare(strict_types=1);

namespace Warp\Type\Cast;

use Warp\Type\TypeInterface;

interface CasterFactoryInterface
{
    public function make(TypeInterface $type): ?CasterInterface;
}
