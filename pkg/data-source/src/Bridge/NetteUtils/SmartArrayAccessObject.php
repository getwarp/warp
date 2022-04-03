<?php

declare(strict_types=1);

namespace Warp\DataSource\Bridge\NetteUtils;

use Nette\SmartObject;
use Nette\Utils\ObjectHelpers;

trait SmartArrayAccessObject
{
    use SmartObject;

    /**
     * @inheritDoc
     * @param string $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->__isset($offset);
    }

    /**
     * @inheritDoc
     * @param string $offset
     * @return mixed
     */
    public function &offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * @inheritDoc
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->__set($offset, $value);
    }

    /**
     * @inheritDoc
     * @param string $offset
     */
    public function offsetUnset($offset): void
    {
        $this->__set($offset, null);
    }

    /**
     * Returns object properties
     * @return array
     */
    public function getProperties(): array
    {
        return array_keys(ObjectHelpers::getMagicProperties(static::class));
    }
}
