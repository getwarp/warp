<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use spaceonfire\Type\Factory\CollectionTypeFactory;
use spaceonfire\Type\Factory\CompositeTypeFactory;

final class CollectionType implements Type
{
    /**
     * @var Type
     */
    private $valueType;
    /**
     * @var Type|null
     */
    private $keyType;
    /**
     * @var Type
     */
    private $iterableType;

    /**
     * CollectionType constructor.
     * @param Type $valueType
     * @param Type|null $keyType
     * @param Type|null $iterableType
     */
    public function __construct(Type $valueType, ?Type $keyType = null, ?Type $iterableType = null)
    {
        $this->valueType = $valueType;
        $this->keyType = $keyType;
        $this->iterableType = $iterableType ?? new BuiltinType(BuiltinType::ITERABLE);
    }

    /**
     * @inheritDoc
     */
    public function check($value): bool
    {
        if (!$this->iterableType->check($value)) {
            return false;
        }

        foreach ($value as $k => $v) {
            if (!$this->valueType->check($v)) {
                return false;
            }

            if ($this->keyType !== null && !$this->keyType->check($k)) {
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
        if ($this->iterableType instanceof InstanceOfType || $this->keyType !== null) {
            return $this->iterableType . '<' . implode(',', array_filter([$this->keyType, $this->valueType])) . '>';
        }

        return $this->valueType . '[]';
    }

    /**
     * @param string $type
     * @return bool
     * @deprecated use dynamic type factory instead. This method will be removed in next major release.
     * @see Factory\TypeFactoryInterface
     */
    public static function supports(string $type): bool
    {
        $factory = new CollectionTypeFactory();
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
        $factory = new CollectionTypeFactory();
        $factory->setParent(CompositeTypeFactory::makeWithDefaultFactories());
        return $factory->make($type);
    }
}
