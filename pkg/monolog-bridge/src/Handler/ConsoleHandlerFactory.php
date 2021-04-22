<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\PsrHandler;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleHandlerFactory implements HandlerFactoryInterface
{
    private ?OutputInterface $output;

    public function __construct(?OutputInterface $output = null)
    {
        $this->output = $output;
    }

    public function supportedTypes(): array
    {
        if ('cli' !== \PHP_SAPI || !\class_exists(ConsoleLogger::class)) {
            return [];
        }

        return ['console'];
    }

    public function make(array $settings): HandlerInterface
    {
        $config = new ConsoleHandlerSettings($settings);

        $output = $config->output ?? $this->output;
        \assert(null !== $output);

        $logger = new ConsoleLogger(
            $output,
            $config->verbosityLevelMap,
            $config->formatLevelMap,
        );

        $handler = new PsrHandler($logger, $config->level, $config->bubble);

        if (null !== $formatter = $config->getFormatter()) {
            $handler->setFormatter($formatter);
        }

        return $handler;
    }
}
