<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\LaminasHydrator\Fixtures;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ObjectPropertyHydrator;
use spaceonfire\Bridge\LaminasHydrator\HydrateConstructorTrait;

final class FixtureConfig
{
    use HydrateConstructorTrait;

    public $foo;
    public $bar;
    public $baz;

    protected static function hydrator(): HydratorInterface
    {
        return new ObjectPropertyHydrator();
    }
}
