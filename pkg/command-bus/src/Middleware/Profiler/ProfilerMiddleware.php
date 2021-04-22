<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Middleware\Profiler;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use spaceonfire\CommandBus\MiddlewareInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @todo: get rid from logger dependency here.
 */
final class ProfilerMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    private Stopwatch $stopwatch;

    /**
     * @var callable(object):bool
     */
    private $predicate;

    private LoggerInterface $logger;

    private string $logLevel;

    /**
     * @param Stopwatch $stopwatch
     * @param null|callable(object):bool $predicate
     * @param LoggerInterface|null $logger
     * @param string $logLevel
     */
    public function __construct(
        Stopwatch $stopwatch,
        ?callable $predicate = null,
        ?LoggerInterface $logger = null,
        string $logLevel = LogLevel::DEBUG
    ) {
        $this->stopwatch = $stopwatch;
        $this->predicate = $predicate ?? static fn (object $message): bool => true;
        $this->logger = $logger ?? new NullLogger();
        $this->logLevel = $logLevel;
    }

    public function execute(object $command, callable $next)
    {
        if (!($this->predicate)($command)) {
            return $next($command);
        }

        $this->startStopwatch($command);

        try {
            return $next($command);
        } finally {
            $this->stopStopwatch($command);
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    private function resolveCommandProfilingEventName(object $command): string
    {
        $eventName = $command instanceof MayBeProfiledMessageInterface ? $command->getProfilingEventName() : null;
        return $eventName ?? \get_class($command);
    }

    private function startStopwatch(object $command): void
    {
        $eventName = $this->resolveCommandProfilingEventName($command);

        $eventCategory = $command instanceof MayBeProfiledMessageInterface ? $command->getProfilingCategory() : null;

        $event = $this->stopwatch->start($eventName, $eventCategory);

        $this->logger->log($this->logLevel, \sprintf('Profiling event %s started', $eventName), [
            'event' => $event,
        ]);
    }

    private function stopStopwatch(object $command): void
    {
        $eventName = $this->resolveCommandProfilingEventName($command);

        $profilingData = $this->stopwatch->stop($eventName);

        $this->logger->log(
            $this->logLevel,
            \sprintf('Profiling event %s finished (%s)', $eventName, (string)$profilingData),
            [
                'event' => $profilingData,
            ]
        );
    }
}
