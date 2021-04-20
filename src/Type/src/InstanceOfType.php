<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;
use spaceonfire\Type\Factory\InstanceOfTypeFactory;

final class InstanceOfType implements Type
{
    /**
     * @var string
     */
    private $className;

    /**
     * InstanceOfType constructor.
     * @param string $className
     */
    public function __construct(string $className)
    {
        if (!class_exists($className) && !interface_exists($className)) {
            throw new InvalidArgumentException(sprintf('Type "%s" is not supported by %s', $className, __CLASS__));
        }

        $this->className = $className;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->className;
    }

    /**
     * @inheritDoc
     */
    public function check($value): bool
    {
        return $value instanceof $this->className;
    }

    /**
     * @param string $type
     * @return bool
     * @deprecated use dynamic type factory instead. This method will be removed in next major release.
     * @see Factory\TypeFactoryInterface
     */
    public static function supports(string $type): bool
    {
        return (new InstanceOfTypeFactory())->supports($type);
    }

    /**
     * @param string $type
     * @return self
     * @deprecated use dynamic type factory instead. This method will be removed in next major release.
     * @see Factory\TypeFactoryInterface
     */
    public static function create(string $type): Type
    {
        return (new InstanceOfTypeFactory())->make($type);
    }
}
