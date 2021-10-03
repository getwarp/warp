<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

use spaceonfire\Type\TypeInterface;

final class ScalarCasterFactory implements CasterFactoryInterface
{
    public function make(TypeInterface $type): ?CasterInterface
    {
        if (ScalarCaster::isScalar($type)) {
            return new ScalarCaster($type);
        }

        return null;
    }
}
