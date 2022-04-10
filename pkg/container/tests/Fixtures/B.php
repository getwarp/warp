<?php

declare(strict_types=1);

namespace Warp\Container\Fixtures;

class B
{
    public A $a;

    public function __construct(A $a)
    {
        $this->a = $a;
    }
}
