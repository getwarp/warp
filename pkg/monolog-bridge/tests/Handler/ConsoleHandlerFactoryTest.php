<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Monolog\Handler;

use Monolog\Logger;
use Monolog\Test\TestCase;
use Psr\Log\LogLevel;
use spaceonfire\Bridge\Monolog\Fixture\FixtureFormatter;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleHandlerFactoryTest extends TestCase
{
    public function testDefault(): void
    {
        $output = new BufferedOutput();
        $factory = new ConsoleHandlerFactory($output);

        self::assertSame(['console'], $factory->supportedTypes());

        $handler = $factory->make([]);

        $handler->handle($this->getRecord(Logger::WARNING, 'warning'));

        self::assertSame('[warning] warning', \trim($output->fetch()));
    }

    public function testConfig(): void
    {
        $output = new BufferedOutput(BufferedOutput::VERBOSITY_VERBOSE);
        $factory = new ConsoleHandlerFactory();

        $handler = $factory->make([
            'output' => $output,
            'verbosity_level_map' => [
                LogLevel::EMERGENCY => OutputInterface::VERBOSITY_NORMAL,
                LogLevel::ALERT => OutputInterface::VERBOSITY_NORMAL,
                LogLevel::CRITICAL => OutputInterface::VERBOSITY_NORMAL,
                LogLevel::ERROR => OutputInterface::VERBOSITY_NORMAL,
                LogLevel::WARNING => OutputInterface::VERBOSITY_NORMAL,
                LogLevel::NOTICE => OutputInterface::VERBOSITY_VERBOSE,
                LogLevel::INFO => OutputInterface::VERBOSITY_VERBOSE,
                LogLevel::DEBUG => OutputInterface::VERBOSITY_VERBOSE,
            ],
            'format_level_map' => [],
            'bubble' => 0,
            'level' => 'info',
        ]);

        $handler->handle($this->getRecord(Logger::DEBUG, 'debug'));
        $handler->handle($this->getRecord(Logger::INFO, 'info'));
        $handler->handle($this->getRecord(Logger::ERROR, 'error'));

        self::assertSame(
            <<<LOG
            [info] info
            [error] error
            LOG,
            \trim($output->fetch())
        );
    }

    public function testSettingsFormatterClass(): void
    {
        $output = new BufferedOutput(BufferedOutput::VERBOSITY_VERBOSE);
        $factory = new ConsoleHandlerFactory($output);

        $handler = $factory->make([
            'formatter' => FixtureFormatter::class,
        ]);

        $handler->handle($this->getRecord(Logger::NOTICE, 'message'));

        self::assertSame(
            <<<LOG
            [notice] message
            LOG,
            \trim($output->fetch())
        );
    }

    public function testSettingsFormatterInstance(): void
    {
        $output = new BufferedOutput(BufferedOutput::VERBOSITY_VERBOSE);
        $factory = new ConsoleHandlerFactory($output);

        $handler = $factory->make([
            'formatter' => new FixtureFormatter(),
        ]);

        $handler->handle($this->getRecord(Logger::NOTICE, 'message'));

        self::assertSame(
            <<<LOG
            [notice] message
            LOG,
            \trim($output->fetch())
        );
    }
}
