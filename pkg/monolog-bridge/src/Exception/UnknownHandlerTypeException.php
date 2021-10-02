<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Monolog\Exception;

use spaceonfire\Exception\FriendlyExceptionTrait;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

final class UnknownHandlerTypeException extends \OutOfRangeException implements FriendlyExceptionInterface
{
    use FriendlyExceptionTrait;

    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * @param string $handlerType
     * @param string[] $supportedTypes
     * @return self
     */
    public static function forHandlerType(string $handlerType, array $supportedTypes = []): self
    {
        // TODO: provide known types to solution.
        return new self(\sprintf('No factory for given monolog handler type "%s".', $handlerType));
    }
}
