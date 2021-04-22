<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Monolog\Handler\PsrHandler;
use Monolog\Test\TestCase;
use spaceonfire\MonologBridge\Exception\UnknownHandlerTypeException;
use Symfony\Component\Console\Output\NullOutput;

class HandlerFactoryAggregateTest extends TestCase
{
    public function testDefault(): void
    {
        $aggregate = new HandlerFactoryAggregate(
            $stream = new StreamHandlerFactory(),
            $console = new ConsoleHandlerFactory(new NullOutput()),
        );

        self::assertTrue($aggregate->supports('stream'));
        self::assertTrue($aggregate->supports('console'));
        self::assertFalse($aggregate->supports('mailer'));

        self::assertSame($stream, $aggregate->get('stream'));
        self::assertSame($console, $aggregate->get('console'));

        self::assertSame([$stream, $console], [...$aggregate]);

        self::assertInstanceOf(PsrHandler::class, $aggregate->make('console', []));
    }

    public function testGetUnknown(): void
    {
        $aggregate = new HandlerFactoryAggregate(
            new StreamHandlerFactory(),
            new ConsoleHandlerFactory(new NullOutput()),
        );

        try {
            $aggregate->get('mailer');
        } catch (\Throwable $e) {
        } finally {
            \assert(isset($e));
            self::assertInstanceOf(UnknownHandlerTypeException::class, $e);
            // TODO: test exception
        }
    }
}
