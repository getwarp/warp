<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Bridge\SymfonyStopwatch;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use spaceonfire\CommandBus\Middleware;
use Symfony\Component\Stopwatch\Stopwatch;

final class ProfilerMiddleware implements Middleware, LoggerAwareInterface
{
    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @var ProfilerMiddlewareMessagePredicate
     */
    private $predicate;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var string
     */
    private $logLevel;

    /**
     * ProfilerMiddleware constructor.
     * @param Stopwatch $stopwatch
     * @param ProfilerMiddlewareMessagePredicate|null $predicate
     * @param LoggerInterface|null $logger
     * @param string $logLevel
     */
    public function __construct(
        Stopwatch $stopwatch,
        ?ProfilerMiddlewareMessagePredicate $predicate = null,
        ?LoggerInterface $logger = null,
        string $logLevel = LogLevel::DEBUG
    ) {
        $this->stopwatch = $stopwatch;
        $this->predicate = $this->preparePredicate($predicate);
        $this->logger = $logger;
        $this->logLevel = $logLevel;
    }

    /**
     * @inheritDoc
     */
    public function execute(object $command, callable $next)
    {
        if (!($this->predicate)($command)) {
            return $next($command);
        }

        $this->startStopwatch($command);

        try {
            $result = $next($command);
        } finally {
            $this->stopStopwatch($command);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    private function preparePredicate(
        ?ProfilerMiddlewareMessagePredicate $predicate
    ): ProfilerMiddlewareMessagePredicate {
        if ($predicate instanceof ProfilerMiddlewareMessagePredicate) {
            return $predicate;
        }

        return new ClosureProfilerMiddlewareMessagePredicate(
            static function (object $message): bool {
                return true;
            }
        );
    }

    private function resolveCommandProfilingEventName(object $command): string
    {
        $eventName = $command instanceof MayBeProfiledMessage
            ? $command->getProfilingEventName()
            : null;
        return $eventName ?? get_class($command) . '_' . spl_object_hash($command);
    }

    private function log(string $message, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->log($this->logLevel, $message, $context);
        }
    }

    private function startStopwatch(object $command): void
    {
        $eventName = $this->resolveCommandProfilingEventName($command);

        $eventCategory = $command instanceof MayBeProfiledMessage ? $command->getProfilingCategory() : null;

        if (null !== $eventCategory) {
            $event = $this->stopwatch->start($eventName, $eventCategory);
        } else {
            $event = $this->stopwatch->start($eventName);
        }

        $this->log(sprintf('Profiling event %s started', $eventName), [
            'event' => $event,
        ]);
    }

    private function stopStopwatch(object $command): void
    {
        $eventName = $this->resolveCommandProfilingEventName($command);

        $profilingData = $this->stopwatch->stop($eventName);

        $this->log(sprintf('Profiling event %s finished (%s)', $eventName, (string)$profilingData), [
            'event' => $profilingData,
        ]);
    }
}
