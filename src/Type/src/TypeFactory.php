<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;

abstract class TypeFactory
{
    /**
     * @var string[]|Type[]
     */
    private const FACTORIES = [
        CollectionType::class,
        DisjunctionType::class,
        ConjunctionType::class,
        InstanceOfType::class,
        BuiltinType::class,
    ];

    /**
     * @codeCoverageIgnore
     */
    final private function __construct()
    {
    }

    public static function create(string $type): Type
    {
        foreach (self::FACTORIES as $factory) {
            if ($factory::supports($type)) {
                return $factory::create($type);
            }
        }

        throw new InvalidArgumentException('Not supported type: ' . $type);
    }
}
