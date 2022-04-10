<?php

declare(strict_types=1);

namespace Warp\Container\Fixtures;

class MyClass
{
    public function methodA(A $a = null)
    {
        return 'foo';
    }

    public static function staticMethodB(B $b)
    {
        return 'bar';
    }

    public function methodBuiltin(int $int)
    {
        return 'foo';
    }

    public function methodBuiltinOptional(int $int = 42)
    {
        return 'foo';
    }

    public function methodBuiltinNullable(?int $int)
    {
        return 'foo';
    }
}
