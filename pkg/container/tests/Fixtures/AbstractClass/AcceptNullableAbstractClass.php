<?php

declare(strict_types=1);

namespace Warp\Container\Fixtures\AbstractClass;

final class AcceptNullableAbstractClass
{
    private ?AbstractClass $abstractClass = null;

    public function __construct(?AbstractClass $abstractClass)
    {
        $this->abstractClass = $abstractClass;
    }

    public function getAbstractClass(): ?AbstractClass
    {
        return $this->abstractClass;
    }
}
