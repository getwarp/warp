<?php

declare(strict_types=1);

namespace spaceonfire\Container\Fixtures;

class B
{
    /**
     * @var A
     */
    private $a;

    /**
     * B constructor.
     * @param A $a
     */
    public function __construct(A $a)
    {
        $this->a = $a;
    }
}
