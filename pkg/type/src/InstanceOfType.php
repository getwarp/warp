<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use spaceonfire\Common\Factory\SingletonStorageTrait;
use spaceonfire\Common\Factory\StaticConstructorInterface;

final class InstanceOfType implements TypeInterface, StaticConstructorInterface
{
    use SingletonStorageTrait;

    /**
     * @var class-string
     */
    private string $className;

    /**
     * @param class-string $className
     */
    private function __construct(string $className)
    {
        $this->className = $className;

        self::singletonAttach($this);
    }

    public function __destruct()
    {
        self::singletonDetach($this);
    }

    public function __toString(): string
    {
        return $this->className;
    }

    public function check($value): bool
    {
        return $value instanceof $this->className;
    }

    /**
     * @param class-string $className
     * @return self
     */
    public static function new(string $className): self
    {
        return self::singletonFetch($className) ?? new self($className);
    }

    /**
     * @param self $value
     * @return string
     */
    protected static function singletonKey($value): string
    {
        return $value->className;
    }
}
