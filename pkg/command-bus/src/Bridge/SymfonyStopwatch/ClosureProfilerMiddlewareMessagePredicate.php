<?php

declare(strict_types=1);

namespace Warp\CommandBus\Bridge\SymfonyStopwatch;

final class ClosureProfilerMiddlewareMessagePredicate implements ProfilerMiddlewareMessagePredicateInterface
{
    /**
     * @var callable
     */
    private $closure;

    /**
     * ClosureProfilerMiddlewareMessagePredicate constructor.
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
