<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Middleware\Logger;

use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Psr\Log\Test\TestLogger;
use spaceonfire\CommandBus\Fixtures\FixtureMayBeLoggedMessage;

class LoggerMiddlewareTest extends TestCase
{
    public function testExecute(): void
    {
        $middleware = new LoggerMiddleware(
            $logger = new TestLogger()
        );

        $message = new FixtureMayBeLoggedMessage();

        $result = $middleware->execute($message, fn (object $message) => 'foo');

        self::assertSame('foo', $result);
        self::assertTrue($logger->hasInfo(\sprintf('Start handling %s command', FixtureMayBeLoggedMessage::class)));
        self::assertTrue($logger->hasInfo(\sprintf('%s command handled successfully', FixtureMayBeLoggedMessage::class)));
    }

    public function testExecuteWithException(): void
    {
        $middleware = new LoggerMiddleware(
            $logger = new TestLogger()
        );

        $message = new FixtureMayBeLoggedMessage();

        $exception = new \RuntimeException('Test Exception');

        try {
            $middleware->execute($message, function (object $message) use ($exception) {
                throw $exception;
            });
        } catch (\Throwable $e) {
        } finally {
            \assert(isset($e));
            self::assertSame($exception, $e);
            self::assertTrue($logger->hasInfo(\sprintf('Start handling %s command', FixtureMayBeLoggedMessage::class)));
            self::assertTrue($logger->hasInfo(\sprintf('Exception thrown during handle of %s command', FixtureMayBeLoggedMessage::class)));
            self::assertTrue($logger->hasErrorThatContains('Test Exception'));
        }
    }

    public function testExecuteWithMayBeLoggedMessage(): void
    {
        $middleware = new LoggerMiddleware(
            $logger = new TestLogger()
        );

        $message = new FixtureMayBeLoggedMessage();
        $message->setBeforeMessage('Before: {command}');
        $message->setAfterMessage('After: {command}');

        $result = $middleware->execute($message, fn (FixtureMayBeLoggedMessage $message) => 'foo');

        self::assertSame('foo', $result);
        self::assertTrue($logger->hasInfo(\sprintf('Before: %s', FixtureMayBeLoggedMessage::class)));
        self::assertTrue($logger->hasInfo(\sprintf('After: %s', FixtureMayBeLoggedMessage::class)));
    }

    public function testExecuteWithMayBeLoggedMessageAndException(): void
    {
        $middleware = new LoggerMiddleware(
            $logger = new TestLogger()
        );

        $message = new FixtureMayBeLoggedMessage();
        $message->setBeforeMessage('Before: {command}');
        $message->setErrorMessage('Error: {command}');

        $exception = new \RuntimeException('Test Exception');

        try {
            $middleware->execute($message, function (FixtureMayBeLoggedMessage $message) use ($exception) {
                throw $exception;
            });
        } catch (\Throwable $e) {
        } finally {
            \assert(isset($e));
            self::assertSame($exception, $e);
            self::assertTrue($logger->hasInfo(\sprintf('Before: %s', FixtureMayBeLoggedMessage::class)));
            self::assertTrue($logger->hasInfo(\sprintf('Error: %s', FixtureMayBeLoggedMessage::class)));
            self::assertTrue($logger->hasErrorThatContains('Test Exception'));
        }
    }

    /**
     * @dataProvider exceptionLogLevelProvider
     * @param \Throwable $exception
     * @param string $expectedLevel
     */
    public function testExecuteWithExceptionLogLevel(\Throwable $exception, string $expectedLevel): void
    {
        $middleware = new LoggerMiddleware(
            $logger = new TestLogger(),
            null,
            LogLevel::INFO,
            [
                \Throwable::class => LogLevel::WARNING,
                \Exception::class => LogLevel::ERROR,
                \ArithmeticError::class => LogLevel::ERROR,
                \LogicException::class => LogLevel::CRITICAL,
                \RuntimeException::class => LogLevel::ALERT,
                \BadFunctionCallException::class => LogLevel::EMERGENCY,
            ]
        );

        $message = new FixtureMayBeLoggedMessage();

        try {
            $middleware->execute($message, function (FixtureMayBeLoggedMessage $message) use ($exception) {
                throw $exception;
            });
        } catch (\Throwable $e) {
        } finally {
            \assert(isset($e));
            self::assertSame($exception, $e);
            self::assertTrue($logger->hasRecords($expectedLevel));
        }
    }

    public function exceptionLogLevelProvider(): array
    {
        return [
            [new \Exception(), LogLevel::ERROR],
            [new \LogicException(), LogLevel::CRITICAL],
            [new \RuntimeException(), LogLevel::ALERT],
            [new \BadFunctionCallException(), LogLevel::EMERGENCY],
            [new \TypeError(), LogLevel::WARNING],
            [new \DivisionByZeroError(), LogLevel::ERROR],
        ];
    }

    public function testInvalidExceptionLogLevel(): void
    {
        $logger = new TestLogger();
        try {
            new LoggerMiddleware(
                $logger,
                null,
                LogLevel::INFO,
                [
                    \Throwable::class => 'blablabla',
                    \Exception::class => LogLevel::ERROR,
                    \ArithmeticError::class => LogLevel::ERROR,
                    \LogicException::class => LogLevel::CRITICAL,
                    \RuntimeException::class => LogLevel::ALERT,
                    \BadFunctionCallException::class => LogLevel::EMERGENCY,
                ]
            );
        } catch (\Throwable $e) {
        } finally {
            \assert(isset($e));
            self::assertInstanceOf(\InvalidArgumentException::class, $e);
        }
        unset($e);

        try {
            new LoggerMiddleware(
                $logger,
                null,
                LogLevel::INFO,
                [
                    \Throwable::class => LogLevel::WARNING,
                    \Exception::class => 'blablabla',
                    \ArithmeticError::class => LogLevel::ERROR,
                    \LogicException::class => LogLevel::CRITICAL,
                    \RuntimeException::class => LogLevel::ALERT,
                    \BadFunctionCallException::class => LogLevel::EMERGENCY,
                ]
            );
        } catch (\Throwable $e) {
        } finally {
            \assert(isset($e));
            self::assertInstanceOf(\InvalidArgumentException::class, $e);
        }
    }

    public function testExecuteWithPredicate(): void
    {
        $redMessage = new FixtureMayBeLoggedMessage();
        $redMessage->setBeforeMessage('Before: red');
        $redMessage->setAfterMessage('After: red');

        $greenMessage = new FixtureMayBeLoggedMessage();
        $greenMessage->setBeforeMessage('Before: green');
        $greenMessage->setAfterMessage('After: green');

        $middleware = new LoggerMiddleware(
            $logger = new TestLogger(),
            static fn ($message) => $message === $greenMessage,
        );

        $next = static fn (FixtureMayBeLoggedMessage $message) => 'foo';

        $middleware->execute($redMessage, $next);
        $middleware->execute($greenMessage, $next);

        self::assertTrue($logger->hasInfo('Before: green'));
        self::assertTrue($logger->hasInfo('After: green'));
        self::assertFalse($logger->hasInfo('Before: red'));
        self::assertFalse($logger->hasInfo('After: red'));
    }
}
