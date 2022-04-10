<?php

/** @noinspection PhpLanguageLevelInspection */

declare(strict_types=1);

namespace Warp\Container\Fixtures\PHP8;

use Warp\Container\Fixtures\A;
use Warp\Container\Fixtures\B;

final class UnionTypes
{
    public function methodAB(A|B $ab)
    {
        return func_get_args();
    }

    public function methodABUnknown(UnknownClass|A|B $ab)
    {
        return func_get_args();
    }

    public function methodNullableAB(A|B|null $ab = null)
    {
        return func_get_args();
    }

    public function methodMixed(mixed $mixed)
    {
        return func_get_args();
    }
}
