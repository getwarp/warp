<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping;

use RuntimeException;
use Warp\CommandBus\ExceptionInterface;

final class FailedToMapCommand extends RuntimeException implements ExceptionInterface
{
    public static function className(string $commandClassName): self
    {
        return new static('Failed to map the class name for command ' . $commandClassName);
    }

    public static function methodName(string $commandClassName): self
    {
        return new static('Failed to map the method name for command ' . $commandClassName);
    }
}
