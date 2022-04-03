<?php

declare(strict_types=1);

namespace Warp\Collection;

use BadMethodCallException;

trait CollectionAliasesTrait
{
    /**
     * Magic methods call
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments = [])
    {
        if (isset(CollectionInterface::ALIASES[$name])) {
            return call_user_func_array([$this, CollectionInterface::ALIASES[$name]], $arguments);
        }

        throw new BadMethodCallException('Call to undefined method ' . static::class . '::' . $name . '()');
    }
}
