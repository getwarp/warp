<?php

declare(strict_types=1);

namespace Warp\CommandBus\Middleware\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Warp\CommandBus\MiddlewareInterface;

final class LoggerMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;

    private string $logLevel;

    /**
     * @var array<class-string<\Throwable>,string>
     */
    private array $exceptionLogLevelMap;

    private string $defaultExceptionLogLevel;

    /**
     * @var callable(object):bool
     */
    private $predicate;

    /**
     * LoggerMiddleware constructor.
     * @param LoggerInterface $logger
     * @param null|callable(object):bool $predicate
     * @param string $logLevel
     * @param array<class-string<\Throwable>,string> $exceptionLogLevelMap
     * @param string $defaultExceptionLogLevel
     */
    public function __construct(
        LoggerInterface $logger,
        ?callable $predicate = null,
        string $logLevel = LogLevel::INFO,
        array $exceptionLogLevelMap = [],
        string $defaultExceptionLogLevel = LogLevel::ERROR
    ) {
        $this->logger = $logger;
        $this->predicate = $predicate ?? static fn (object $message): bool => true;
        $this->logLevel = $logLevel;
        $this->exceptionLogLevelMap = $exceptionLogLevelMap;
        \uasort($this->exceptionLogLevelMap, [$this, 'compareLogLevel']);
        $this->defaultExceptionLogLevel = $defaultExceptionLogLevel;
    }

    public function execute(object $command, callable $next)
    {
        if (!($this->predicate)($command)) {
            return $next($command);
        }

        $this->logBefore($command);

        try {
            $result = $next($command);

            $this->logAfter($command);
        } catch (\Throwable $exception) {
            $this->logError($command, $exception);
            throw $exception;
        }

        return $result;
    }

    private function compareLogLevel(string $a, string $b): int
    {
        $logLevelWeights = [
            LogLevel::DEBUG => 1,
            LogLevel::INFO => 2,
            LogLevel::NOTICE => 3,
            LogLevel::WARNING => 4,
            LogLevel::ERROR => 5,
            LogLevel::CRITICAL => 6,
            LogLevel::ALERT => 7,
            LogLevel::EMERGENCY => 8,
        ];

        $aWeight = $logLevelWeights[$a] ?? null;
        $bWeight = $logLevelWeights[$b] ?? null;

        if (null === $aWeight) {
            throw new \InvalidArgumentException(\sprintf('Unknown log level: %s.', $a));
        }

        if (null === $bWeight) {
            throw new \InvalidArgumentException(\sprintf('Unknown log level: %s.', $b));
        }

        return $bWeight <=> $aWeight;
    }

    private function logBefore(object $command): void
    {
        $message = $command instanceof MayBeLoggedMessageInterface ? $command->renderBeforeMessage() : null;

        $this->logger->log($this->logLevel, $message ?? \sprintf('Start handling %s command', \get_class($command)));
    }

    private function logAfter(object $command): void
    {
        $message = $command instanceof MayBeLoggedMessageInterface ? $command->renderAfterMessage() : null;

        $this->logger->log(
            $this->logLevel,
            $message ?? \sprintf('%s command handled successfully', \get_class($command))
        );
    }

    private function logError(object $command, \Throwable $exception): void
    {
        $message = $command instanceof MayBeLoggedMessageInterface ? $command->renderErrorMessage() : null;

        $this->logger->log(
            $this->logLevel,
            $message ?? \sprintf('Exception thrown during handle of %s command', \get_class($command))
        );
        $this->logger->log(
            $this->getLogLevelForException($exception),
            $exception->getMessage(),
            [
                'exception' => $exception,
            ]
        );
    }

    private function getLogLevelForException(\Throwable $exception): string
    {
        $logLevel = $this->exceptionLogLevelMap[\get_class($exception)] ?? null;

        if (null !== $logLevel) {
            return $logLevel;
        }

        foreach ($this->exceptionLogLevelMap as $class => $logLevel) {
            if ($exception instanceof $class) {
                return $logLevel;
            }
        }

        return $this->defaultExceptionLogLevel;
    }
}
