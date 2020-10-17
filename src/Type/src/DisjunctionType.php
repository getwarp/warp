<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use spaceonfire\Type\Factory\CompositeTypeFactory;
use spaceonfire\Type\Factory\DisjunctionTypeFactory;

final class DisjunctionType extends AbstractAggregatedType
{
    public const DELIMITER = '|';

    /**
     * DisjunctionType constructor.
     * @param Type[] $disjuncts
     */
    public function __construct(iterable $disjuncts)
    {
        parent::__construct($disjuncts, self::DELIMITER);
    }

    /**
     * @inheritDoc
     */
    public function check($value): bool
    {
        foreach ($this->types as $type) {
            if ($type->check($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $type
     * @return bool
     * @deprecated use dynamic type factory instead. This method will be removed in next major release.
     * @see Factory\TypeFactoryInterface
     */
    public static function supports(string $type): bool
    {
        $factory = new DisjunctionTypeFactory();
        $factory->setParent(CompositeTypeFactory::makeWithDefaultFactories());
        return $factory->supports($type);
    }

    /**
     * @param string $type
     * @return self
     * @deprecated use dynamic type factory instead. This method will be removed in next major release.
     * @see Factory\TypeFactoryInterface
     */
    public static function create(string $type): Type
    {
        $factory = new DisjunctionTypeFactory();
        $factory->setParent(CompositeTypeFactory::makeWithDefaultFactories());
        return $factory->make($type);
    }
}
