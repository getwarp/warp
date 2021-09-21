<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Laminas\Hydrator\HydratorInterface;
use Psr\Log\LoggerInterface;
use spaceonfire\LaminasHydratorBridge\NamingStrategy\AliasNamingStrategy;
use spaceonfire\LaminasHydratorBridge\StdClassHydrator;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleHandlerFactory extends AbstractPsrHandlerFactory
{
    /**
     * @var OutputInterface|null
     */
    private $output;

    /**
     * ConsoleHandlerFactory constructor.
     * @param OutputInterface|null $output
     */
    public function __construct(?OutputInterface $output = null)
    {
        $this->output = $output;
    }

    /**
     * @inheritDoc
     */
    public function supportedTypes(): array
    {
        if ('cli' !== PHP_SAPI || !class_exists(ConsoleLogger::class)) {
            return [];
        }

        return ['console'];
    }

    /**
     * @inheritDoc
     */
    protected function makeLogger(array $parameters, CompositeHandlerFactory $factory): LoggerInterface
    {
        $parametersHydrated = $this->hydrateParameters($parameters);

        return new ConsoleLogger(
            $parametersHydrated->output ?? $this->output,
            $parametersHydrated->verbosityLevelMap ?? [],
            $parametersHydrated->formatLevelMap ?? []
        );
    }

    protected function getParametersHydrator(): ?HydratorInterface
    {
        $hydrator = new StdClassHydrator();

        $hydrator->setNamingStrategy(new AliasNamingStrategy([
            'verbosityLevelMap' => ['verbosity_level_map', 'verbosity-level-map'],
            'formatLevelMap' => ['format_level_map', 'format-level-map'],
        ]));

        return $hydrator;
    }
}
