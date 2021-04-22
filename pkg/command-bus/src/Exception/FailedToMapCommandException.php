<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Exception;

final class FailedToMapCommandException extends \RuntimeException
{
    public static function className(string $commandClass): self
    {
        return new self(\sprintf('Failed to map handler classname for command %s.', $commandClass));
    }

    public static function methodName(string $commandClass): self
    {
        return new self(\sprintf('Failed to map handler method name for command %s.', $commandClass));
    }
}
