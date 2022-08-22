<?php

declare(strict_types=1);

namespace Vendor;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Warp\Bridge\LaminasHydrator\HydrateConstructorTrait;

final class ExampleConfig
{
    use HydrateConstructorTrait;

    /**
     * @var mixed
     */
    public $foo;

    /**
     * @var mixed
     */
    public $bar;

    /**
     * @var mixed
     */
    public $baz;

    protected static function hydrator(): HydratorInterface
    {
        return new ObjectPropertyHydrator();
    }
}
