<?php

declare(strict_types=1);

namespace Warp\CommandBus\Bridge\PsrLog;

use ArithmeticError;
use BadFunctionCallException;
use DivisionByZeroError;
use Exception;
use InvalidArgumentException;
use LogicException;
use Psr\Log\LogLevel;
use Psr\Log\Test\TestLogger;
use RuntimeException;
use Warp\CommandBus\_Fixtures\Bridge\PsrLog\FixtureMayBeLoggedMessage;
use Warp\CommandBus\AbstractTestCase;
use Throwable;
use TypeError;

class LoggerMiddlewareTest extends AbstractTestCase
{
    public function testExecute(): void
    {
        $middleware = new LoggerMiddleware(
            $logger = new TestLogger()
        );

        $message = new FixtureMayBeLoggedMessage();

        $result = $middleware->execute($message, function (object $message) {
            return 'foo';
        });

        self::assertSame('foo', $result);
        self::assertTrue($logger->hasInfo(sprintf('Start handling %s command', FixtureMayBeLoggedMessage::class)));
        self::assertTrue($logger->hasInfo(sprintf('%s command handled successfully', FixtureMayBeLoggedMessage::class)));
    }

    public function testExecuteWithException(): void
    {
        $middleware = new LoggerMiddleware(
            $logger = new TestLogger()
        );

        $message = new FixtureMayBeLoggedMessage();

        $exception = new RuntimeException('Test Exception');

        try {
            $middleware->execute($message, function (object $message) use ($exception) {
                throw $exception;
            });
        } catch (Throwable $e) {
            self::assertSame($exception, $e);
            self::assertTrue($logger->hasInfo(sprintf('Start handling %s command', FixtureMayBeLoggedMessage::class)));
            self::assertTrue($logger->hasInfo(sprintf('Exception thrown during handle of %s command', FixtureMayBeLoggedMessage::class)));
            self::assertTrue($logger->hasErrorRecords());
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

        $result = $middleware->execute($message, function (FixtureMayBeLoggedMessage $message) {
            return 'foo';
        });

        self::assertSame('foo', $result);
        self::assertTrue($logger->hasInfo($message->renderBeforeMessage()));
        self::assertTrue($logger->hasInfo($message->renderAfterMessage()));
    }

    public function testExecuteWithMayBeLoggedMessageAndException(): void
    {
        $middleware = new LoggerMiddleware(
            $logger = new TestLogger()
        );

        $message = new FixtureMayBeLoggedMessage();
        $message->setBeforeMessage('Before: {command}');
        $message->setErrorMessage('Error: {command}');

        $exception = new RuntimeException('Test Exception');

        try {
            $middleware->execute($message, function (FixtureMayBeLoggedMessage $message) use ($exception) {
                throw $exception;
            });
        } catch (Throwable $e) {
            self::assertSame($exception, $e);
            self::assertTrue($logger->hasInfo($message->renderBeforeMessage()));
            self::assertTrue($logger->hasInfo($message->renderErrorMessage()));
            self::assertTrue($logger->hasErrorRecords());
        }
    }

    /**
     * @dataProvider exceptionLogLevelProvider
     * @param Throwable $exception
     * @param string $expectedLevel
     */
    public function testExecuteWithExceptionLogLevel(Throwable $exception, string $expectedLevel): void
    {
        $middleware = new LoggerMiddleware(
            $logger = new TestLogger(),
            null,
            LogLevel::INFO,
            [
                Throwable::class => LogLevel::WARNING,
                Exception::class => LogLevel::ERROR,
                ArithmeticError::class => LogLevel::ERROR,
                LogicException::class => LogLevel::CRITICAL,
                RuntimeException::class => LogLevel::ALERT,
                BadFunctionCallException::class => LogLevel::EMERGENCY,
            ]
        );

        $message = new FixtureMayBeLoggedMessage();

        try {
            $middleware->execute($message, function (FixtureMayBeLoggedMessage $message) use ($exception) {
                throw $exception;
            });
        } catch (Throwable $e) {
            self::assertSame($exception, $e);
            self::assertTrue($logger->hasRecords($expectedLevel));
        }
    }

    public function exceptionLogLevelProvider(): array
    {
        return [
            [new Exception(), LogLevel::ERROR],
            [new LogicException(), LogLevel::CRITICAL],
            [new RuntimeException(), LogLevel::ALERT],
            [new BadFunctionCallException(), LogLevel::EMERGENCY],
            [new TypeError(), LogLevel::WARNING],
            [new DivisionByZeroError(), LogLevel::ERROR],
        ];
    }

    public function testInvalidExceptionLogLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new LoggerMiddleware(
            $logger = new TestLogger(),
            null,
            LogLevel::INFO,
            [
                Throwable::class => LogLevel::WARNING,
                Exception::class => LogLevel::ERROR,
                ArithmeticError::class => LogLevel::ERROR,
                LogicException::class => 'unknown log level',
                RuntimeException::class => LogLevel::ALERT,
                BadFunctionCallException::class => LogLevel::EMERGENCY,
            ]
        );
    }

    public function testExecuteWithPredicate(): void
    {
        $redMessage = new FixtureMayBeLoggedMessage();
        $redMessage->setBeforeMessage('Before: red');
        $redMessage->setAfterMessage('After: red');

        $greenMessage = new FixtureMayBeLoggedMessage();
        $greenMessage->setBeforeMessage('Before: green');
        $greenMessage->setAfterMessage('After: green');

        $predicateProphecy = $this->prophesize(LoggerMiddlewareMessagePredicateInterface::class);
        $predicateProphecy->__invoke($redMessage)->willReturn(false);
        $predicateProphecy->__invoke($greenMessage)->willReturn(true);

        $middleware = new LoggerMiddleware(
            $logger = new TestLogger(),
            $predicateProphecy->reveal()
        );

        $next = static function (FixtureMayBeLoggedMessage $message) {
            return 'foo';
        };

        $middleware->execute($redMessage, $next);
        $middleware->execute($greenMessage, $next);

        self::assertTrue($logger->hasInfo($greenMessage->renderBeforeMessage()));
        self::assertTrue($logger->hasInfo($greenMessage->renderAfterMessage()));
        self::assertFalse($logger->hasInfo($redMessage->renderBeforeMessage()));
        self::assertFalse($logger->hasInfo($redMessage->renderAfterMessage()));
    }
}
