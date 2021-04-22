<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use spaceonfire\Common\Factory\SingletonStorageTrait;
use spaceonfire\Common\Factory\StaticConstructorInterface;

final class MixedType implements TypeInterface, StaticConstructorInterface
{
    use SingletonStorageTrait;

    public const NAME = 'mixed';

    private function __construct()
    {
        self::singletonAttach($this);
    }

    public function __destruct()
    {
        self::singletonDetach($this);
    }

    public function __toString(): string
    {
        return self::NAME;
    }

    public function check($value): bool
    {
        return true;
    }

    public static function new(): self
    {
        return self::singletonFetch(self::NAME) ?? new self();
    }

    /**
     * @param self $value
     * @return string
     */
    protected static function singletonKey($value): string
    {
        return self::NAME;
    }
}
