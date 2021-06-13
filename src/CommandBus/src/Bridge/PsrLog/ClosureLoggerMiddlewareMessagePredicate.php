<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Bridge\PsrLog;

final class ClosureLoggerMiddlewareMessagePredicate implements LoggerMiddlewareMessagePredicateInterface
{
    /**
     * @var callable
     */
    private $closure;

    /**
     * ClosureLoggerMiddlewareMessagePredicate constructor.
     * @param callable $closure
     */
    public function __construct(callable $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(object $message): bool
    {
        return ($this->closure)($message);
    }
}
