<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use spaceonfire\Type\Factory\CompositeTypeFactory;
use spaceonfire\Type\Factory\DisjunctionTypeFactory;
use Webmozart\Assert\Assert;

final class DisjunctionType implements Type
{
    public const DELIMITER = '|';

    /**
     * @var Type[]
     */
    private $disjuncts;

    /**
     * DisjunctionType constructor.
     * @param Type[] $disjuncts
     */
    public function __construct(array $disjuncts)
    {
        Assert::allIsInstanceOf($disjuncts, Type::class);
        $this->disjuncts = $disjuncts;
    }

    /**
     * @inheritDoc
     */
    public function check($value): bool
    {
        foreach ($this->disjuncts as $type) {
            if ($type->check($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return implode(self::DELIMITER, array_map(static function (Type $type): string {
            return (string)$type;
        }, $this->disjuncts));
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
