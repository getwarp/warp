<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

trait CasterFactoryAwareTrait
{
    protected ?CasterFactoryInterface $factory = null;

    public function setFactory(CasterFactoryInterface $factory): void
    {
        $this->factory = $factory;
    }
}
