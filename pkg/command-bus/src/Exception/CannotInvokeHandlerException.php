<?php

declare(strict_types=1);

namespace Warp\CommandBus\Exception;

use Warp\Exception\FriendlyExceptionTrait;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

/**
 * Thrown when a specific handler object can not be used on a command object.
 *
 * The most common reason is the receiving method is missing or incorrectly named.
 */
final class CannotInvokeHandlerException extends \BadMethodCallException implements FriendlyExceptionInterface
{
    use FriendlyExceptionTrait;

    private object $command;

    private function __construct(object $command, string $message, ?string $solution = null)
    {
        parent::__construct($message);

        $this->command = $command;
        $this->solution = $solution;
    }

    public static function methodNotExists(object $command, string $handlerClass, string $handlerMethod): self
    {
        return new self(
            $command,
            \sprintf(
                'Handler %s::%s() matched for command %s is not exists.',
                $handlerClass,
                $handlerMethod,
                \get_class($command),
            ),
            'Check handler method existence or mapping correctness.',
        );
    }

    /**
     * Returns the command that could not be invoked
     */
    public function getCommand(): object
    {
        return $this->command;
    }

    protected static function getDefaultName(): string
    {
        return 'Cannot invoke handler';
    }
}
