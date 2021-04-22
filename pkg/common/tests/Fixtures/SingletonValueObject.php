<?php

declare(strict_types=1);

namespace spaceonfire\Common\Fixtures;

use spaceonfire\Common\Factory\SingletonStorageTrait;
use spaceonfire\Common\Factory\StaticConstructorInterface;

final class SingletonValueObject implements StaticConstructorInterface
{
    use SingletonStorageTrait;

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;

        self::singletonAttach($this);
    }

    public function __destruct()
    {
        self::singletonDetach($this);
    }

    public static function new(string $value): self
    {
        return self::singletonFetch($value) ?? new self($value);
    }

    /**
     * @param self $value
     * @return string
     */
    protected static function singletonKey($value): string
    {
        return $value->value;
    }
}
