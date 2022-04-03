<?php

declare(strict_types=1);

namespace Warp\DataSource;

trait JsonSerializableObjectTrait
{
    abstract public function getProperties(): array;

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $properties = $this->getProperties();
        $result = [];
        foreach ($properties as $property) {
            $result[$property] = $this[$property];
        }
        return $result;
    }
}
