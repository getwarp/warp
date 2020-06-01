<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;

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
        if (!self::supports($className)) {
            throw new InvalidArgumentException(sprintf('Type "%s" is not supported by %s', $className, __CLASS__));
        }

        $this->className = $className;
    }

    /**
     * @inheritDoc
     */
    public function check($value): bool
    {
        return $value instanceof $this->className;
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
    public static function supports(string $type): bool
    {
        return class_exists($type) || interface_exists($type);
    }

    /**
     * @inheritDoc
     */
    public static function create(string $type): Type
    {
        return new self($type);
    }
}
