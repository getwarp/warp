<?php

declare(strict_types=1);

namespace spaceonfire\Common\Factory;

trait SingletonStorageTrait
{
    /**
     * @var array<class-string<static>,array<string,\WeakReference<static>>>
     */
    private static array $storage = [];

    abstract protected static function singletonKey($value): string;

    /**
     * @param string $key
     * @return static|null
     */
    private static function singletonFetch(string $key): ?self
    {
        $ref = self::$storage[static::class][$key] ?? null;

        if (null === $ref) {
            return null;
        }

        return $ref->get();
    }

    private static function singletonAttach(self $item): void
    {
        $key = static::singletonKey($item);
        self::$storage[\get_class($item)][$key] = \WeakReference::create($item);
    }

    private static function singletonDetach(self $item): void
    {
        $key = static::singletonKey($item);
        unset(self::$storage[\get_class($item)][$key]);
    }
}
