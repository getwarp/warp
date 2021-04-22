<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Psr\Log\LogLevel;
use spaceonfire\Bridge\LaminasHydrator\NamingStrategy\AliasNamingStrategy;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @phpstan-type PsrLogLevel=LogLevel::*
 * @phpstan-type Verbosity=OutputInterface::VERBOSITY_QUIET|OutputInterface::VERBOSITY_NORMAL|OutputInterface::VERBOSITY_VERBOSE|OutputInterface::VERBOSITY_VERY_VERBOSE|OutputInterface::VERBOSITY_DEBUG
 * @phpstan-type Format=ConsoleLogger::INFO|ConsoleLogger::ERROR
 */
final class ConsoleHandlerSettings extends AbstractHandlerSettings
{
    public ?OutputInterface $output = null;

    /**
     * @var array<PsrLogLevel,Verbosity>
     */
    public array $verbosityLevelMap = [];

    /**
     * @var array<PsrLogLevel,Format>
     */
    public array $formatLevelMap = [];

    protected static function hydrator(): HydratorInterface
    {
        $hydrator = new ObjectPropertyHydrator();

        $hydrator->setNamingStrategy(new AliasNamingStrategy([
            'verbosityLevelMap' => ['verbosity_level_map', 'verbosity-level-map'],
            'formatLevelMap' => ['format_level_map', 'format-level-map'],
        ]));

        $hydrator->addStrategy('bubble', self::booleanStrategy());
        $hydrator->addStrategy('level', self::levelStrategy());

        return $hydrator;
    }
}
