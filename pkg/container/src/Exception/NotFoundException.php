<?php

declare(strict_types=1);

namespace spaceonfire\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function alias(string $alias): self
    {
        return new self(\sprintf('Alias (%s) is not being managed by the container.', $alias));
    }

    public static function factory(string $class): self
    {
        return new self(\sprintf('Factory for class %s not found.', $class));
    }
}
