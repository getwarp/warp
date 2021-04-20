<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus;

use BadMethodCallException;
use Throwable;
use function get_class;

/**
 * Thrown when a specific handler object can not be used on a command object.
 *
 * The most common reason is the receiving method is missing or incorrectly named.
 */
final class CanNotInvokeHandler extends BadMethodCallException implements Exception
{
    /**
     * @var object
     */
    private $command;

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function forCommand(object $command, string $reason): self
    {
        $type = get_class($command);

        $exception = new self(sprintf('Could not invoke handler for command %s for reason: %s', $type, $reason));
        $exception->command = $command;

        return $exception;
    }

    /**
     * Returns the command that could not be invoked
     */
    public function getCommand(): object
    {
        return $this->command;
    }
}
