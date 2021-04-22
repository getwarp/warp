<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Monolog\Handler\PsrHandler;
use Monolog\Logger;
use Monolog\Test\TestCase;
use spaceonfire\MonologBridge\Fixture\FixtureFormatter;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\BufferedOutput;

class FingersCrossedHandlerFactoryTest extends TestCase
{
    public function testInfo(): void
    {
        $factory = new FingersCrossedHandlerFactory();

        self::assertContains('fingers_crossed', $factory->supportedTypes());
    }

    public function testMakeWithoutFactory(): void
    {
        $factory = new FingersCrossedHandlerFactory();
        $this->expectException(\RuntimeException::class);
        $factory->make([]);
    }

    /**
     * @dataProvider makeSettingsProvider()
     */
    public function testMake(array $settings, ?BufferedOutput $output = null): void
    {
        $output ??= new BufferedOutput(BufferedOutput::VERBOSITY_DEBUG);
        $aggregate = new HandlerFactoryAggregate(new ConsoleHandlerFactory($output));
        $factory = new FingersCrossedHandlerFactory();
        $factory = $factory->withContextFactory($aggregate);

        $handler = $factory->make($settings);

        $handler->handle($this->getRecord(Logger::DEBUG, 'debug'));
        $handler->handle($this->getRecord(Logger::INFO, 'info'));
        $handler->handle($this->getRecord(Logger::ERROR, 'error'));

        self::assertSame(
            <<<LOG
            [debug] debug
            [info] info
            [error] error
            LOG,
            \trim($output->fetch())
        );
    }

    public function makeSettingsProvider(): \Generator
    {
        yield [
            [
                'handler' => 'console',
                'activation_strategy' => Logger::ERROR,
                'formatter' => FixtureFormatter::class,
            ],
            null,
        ];

        yield [
            [
                'handler' => [
                    'driver' => 'console',
                ],
                'activation_strategy' => Logger::ERROR,
            ],
            null,
        ];

        $output = new BufferedOutput(BufferedOutput::VERBOSITY_DEBUG);

        yield [
            [
                'handler' => new PsrHandler(new ConsoleLogger($output)),
                'activation_strategy' => Logger::ERROR,
            ],
            $output,
        ];

        $output = new BufferedOutput(BufferedOutput::VERBOSITY_DEBUG);

        yield [
            [
                'handler' => static fn () => new PsrHandler(new ConsoleLogger($output)),
                'activation_strategy' => Logger::ERROR,
            ],
            $output,
        ];
    }

    public function testMakeInvalidHandler(): void
    {
        $output ??= new BufferedOutput(BufferedOutput::VERBOSITY_DEBUG);
        $aggregate = new HandlerFactoryAggregate(new ConsoleHandlerFactory($output));
        $factory = new FingersCrossedHandlerFactory();
        $factory = $factory->withContextFactory($aggregate);

        $this->expectException(\InvalidArgumentException::class);

        $factory->make([
            'handler' => null,
        ]);
    }
}
