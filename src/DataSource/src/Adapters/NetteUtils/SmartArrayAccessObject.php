<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\NetteUtils;

use Nette\SmartObject;
use Nette\Utils\ObjectHelpers;

trait SmartArrayAccessObject
{
    use SmartObject;

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return $this->__isset($offset);
    }

    /**
     * @inheritDoc
     */
    public function &offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->__set($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        $this->__set($offset, null);
    }

    public function getProperties(): array
    {
        return array_keys(ObjectHelpers::getMagicProperties(static::class));
    }
}
