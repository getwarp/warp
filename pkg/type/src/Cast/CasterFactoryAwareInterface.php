<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

interface CasterFactoryAwareInterface
{
    public function setFactory(CasterFactoryInterface $factory): void;
}
