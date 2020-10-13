<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use spaceonfire\Type\Factory\CompositeTypeFactory;

/**
 * Class TypeFactory
 * @package spaceonfire\Type
 * @deprecated use dynamic type factory instead. This class will be removed in next major release.
 * @see Factory\TypeFactoryInterface
 */
abstract class TypeFactory
{
    /**
     * @codeCoverageIgnore
     */
    final private function __construct()
    {
    }

    public static function create(string $type): Type
    {
        return CompositeTypeFactory::makeWithDefaultFactories()->make($type);
    }
}
