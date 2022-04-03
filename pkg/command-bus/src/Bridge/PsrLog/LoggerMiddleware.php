<?php

declare(strict_types=1);

namespace Warp\CommandBus\Bridge\PsrLog;

use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;
use Warp\CommandBus\MiddlewareInterface;

final class LoggerMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $logLevel;

    /**
     * @var array<string,string>
     */
    private $exceptionLogLevelMap;

    /**
     * @var string
     */
    private $defaultExceptionLogLevel;

    /**
     * @var LoggerMiddlewareMessagePredicateInterface
     */
    private $predicate;

    /**
     * LoggerMiddleware constructor.
     * @param LoggerInterface $logger
     * @param LoggerMiddlewareMessagePredicateInterface|null $predicate
     * @param string $logLevel
     * @param array $exceptionLogLevelMap
     * @param string $defaultExceptionLogLevel
     */
    public function __construct(
        LoggerInterface $logger,
        ?LoggerMiddlewareMessagePredicateInterface $predicate = null,
        string $logLevel = LogLevel::INFO,
        array $exceptionLogLevelMap = [],
        string $defaultExceptionLogLevel = LogLevel::ERROR
    ) {
        $this->logger = $logger;
        $this->predicate = $this->preparePredicate($predicate);
        $this->logLevel = $logLevel;
        $this->exceptionLogLevelMap = $exceptionLogLevelMap;
        uasort($this->exceptionLogLevelMap, [$this, 'compareLogLevel']);
        $this->defaultExceptionLogLevel = $defaultExceptionLogLevel;
    }

    /**
     * @inheritDoc
     * @return mixed|null
     * @throws Throwable
     */
    public function execute(object $command, callable $next)
    {
        if (!($this->predicate)($command)) {
            return $next($command);
        }

        $this->logBefore($command);

        try {
            $result = $next($command);

            $this->logAfter($command);
        } catch (Throwable $exception) {
            $this->logError($command, $exception);
            throw $exception;
        }

        return $result;
    }

    private function preparePredicate(
        ?LoggerMiddlewareMessagePredicateInterface $predicate
    ): LoggerMiddlewareMessagePredicateInterface {
        if ($predicate instanceof LoggerMiddlewareMessagePredicateInterface) {
            return $predicate;
        }

        return new ClosureLoggerMiddlewareMessagePredicate(
            static function (object $message): bool {
                return true;
            }
        );
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

        [$aWeight, $bWeight] = array_map(static function ($logLevel) use ($logLevelWeights) {
            if (!isset($logLevelWeights[$logLevel])) {
                throw new InvalidArgumentException(sprintf('Unknown log level: %s', $logLevel));
            }

            return $logLevelWeights[$logLevel];
        }, [$a, $b]);

        if ($aWeight === $bWeight) {
            return 0;
        }

        return $aWeight < $bWeight ? 1 : -1;
    }

    private function logBefore(object $command): void
    {
        \assert(null !== $this->logger);

        $message = $command instanceof MayBeLoggedMessageInterface
            ? $command->renderBeforeMessage()
            : null;

        $this->logger->log($this->logLevel, $message ?? sprintf('Start handling %s command', get_class($command)));
    }

    private function logAfter(object $command): void
    {
        \assert(null !== $this->logger);

        $message = $command instanceof MayBeLoggedMessageInterface
            ? $command->renderAfterMessage()
            : null;

        $this->logger->log(
            $this->logLevel,
            $message ?? sprintf('%s command handled successfully', get_class($command))
        );
    }

    private function logError(object $command, Throwable $exception): void
    {
        \assert(null !== $this->logger);

        $message = $command instanceof MayBeLoggedMessageInterface
            ? $command->renderErrorMessage()
            : null;

        $this->logger->log(
            $this->logLevel,
            $message ?? sprintf('Exception thrown during handle of %s command', get_class($command))
        );
        $this->logger->log(
            $this->getLogLevelForException($exception),
            $exception->getMessage(),
            [
                'exception' => $exception,
            ]
        );
    }

    private function getLogLevelForException(Throwable $exception): string
    {
        $logLevel = $this->exceptionLogLevelMap[get_class($exception)] ?? null;

        if (null === $logLevel) {
            $parents = array_flip(array_merge(class_parents($exception) ?: [], class_implements($exception) ?: []));
            $intersection = array_intersect_key($this->exceptionLogLevelMap, $parents);
            $logLevel = array_values($intersection)[0] ?? null;
        }

        return $logLevel ?? $this->defaultExceptionLogLevel;
    }
}
