<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use spaceonfire\Type\Factory\CompositeTypeFactory;
use spaceonfire\Type\Factory\ConjunctionTypeFactory;

final class ConjunctionType extends AbstractAggregatedType
{
    public const DELIMITER = '&';

    /**
     * ConjunctionType constructor.
     * @param Type[] $conjuncts
     */
    public function __construct(iterable $conjuncts)
    {
        parent::__construct($conjuncts, self::DELIMITER);
    }

    /**
     * @inheritDoc
     */
    public function check($value): bool
    {
        foreach ($this->types as $type) {
            if (!$type->check($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $type
     * @return bool
     * @deprecated use dynamic type factory instead. This method will be removed in next major release.
     * @see Factory\TypeFactoryInterface
     */
    public static function supports(string $type): bool
    {
        $factory = new ConjunctionTypeFactory();
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
        $factory = new ConjunctionTypeFactory();
        $factory->setParent(CompositeTypeFactory::makeWithDefaultFactories());
        return $factory->make($type);
    }
}
