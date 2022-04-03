<?php

declare(strict_types=1);

namespace Warp\Container\Fixtures;

class MyClass
{
    public function method(A $a = null)
    {
        return 'foo';
    }

    public static function staticMethod(B $b)
    {
        return 'bar';
    }

    public function methodForResolve(B $b, A $a, int $int = 42)
    {
    }
}
