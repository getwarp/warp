<?php

declare(strict_types=1);

namespace Warp\Type\Cast;

use Warp\Type\TypeInterface;
use Warp\Type\UnionType;

final class UnionTypeCasterFactory implements CasterFactoryInterface, CasterFactoryAwareInterface
{
    use CasterFactoryAwareTrait;

    public function make(TypeInterface $type): ?CasterInterface
    {
        if (!$type instanceof UnionType) {
            return null;
        }

        if (null === $this->factory) {
            return null;
        }

        $casters = [];

        foreach ($type as $subType) {
            $caster = $this->factory->make($subType);

            if (null === $caster) {
                continue;
            }

            $casters[] = $caster;
        }

        return [] === $casters ? null : new CasterAggregate(...$casters);
    }
}
