<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Exception;

use InvalidArgumentException;
use Throwable;

final class UnknownHandlerTypeException extends InvalidArgumentException
{
    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function forHandlerType(string $handlerType): self
    {
        return new self(sprintf('No factory for given monolog handler type "%s"', $handlerType));
    }
}
