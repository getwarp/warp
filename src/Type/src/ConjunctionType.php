<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use spaceonfire\Type\Factory\CompositeTypeFactory;
use spaceonfire\Type\Factory\ConjunctionTypeFactory;
use Webmozart\Assert\Assert;

final class ConjunctionType implements Type
{
    public const DELIMITER = '&';

    /**
     * @var Type[]
     */
    private $conjuncts;

    /**
     * ConjunctionType constructor.
     * @param Type[] $conjuncts
     */
    public function __construct(array $conjuncts)
    {
        Assert::allIsInstanceOf($conjuncts, Type::class);
        $this->conjuncts = $conjuncts;
    }

    /**
     * @inheritDoc
     */
    public function check($value): bool
    {
        foreach ($this->conjuncts as $type) {
            if (!$type->check($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return implode(self::DELIMITER, array_map(static function (Type $type): string {
            return (string)$type;
        }, $this->conjuncts));
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
