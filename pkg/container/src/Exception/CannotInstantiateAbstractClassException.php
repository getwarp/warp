<?php

declare(strict_types=1);

namespace spaceonfire\Container\Exception;

class CannotInstantiateAbstractClassException extends ContainerException
{
    public function __construct(string $className, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(\sprintf('Cannot instantiate abstract class %s.', $className), $code, $previous);
    }
}
