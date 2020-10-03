<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Laminas\Hydrator\HydratorInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use spaceonfire\LaminasHydratorBridge\NamingStrategy\AliasNamingStrategy;
use spaceonfire\LaminasHydratorBridge\StdClassHydrator;
use spaceonfire\LaminasHydratorBridge\Strategy\BooleanStrategy;

final class StreamHandlerFactory extends AbstractHandlerFactory
{
    /**
     * @inheritDoc
     */
    public function supportedTypes(): array
    {
        return [
            'stream',
            StreamHandler::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function make(array $parameters, CompositeHandlerFactory $factory): HandlerInterface
    {
        $parametersHydrated = $this->hydrateParameters($parameters);

        return new StreamHandler(
            $parametersHydrated->stream,
            $parametersHydrated->level ?? Logger::DEBUG,
            $parametersHydrated->bubble ?? true,
            $parametersHydrated->filePermission ?? null,
            $parametersHydrated->useLocking ?? false
        );
    }

    protected function getParametersHydrator(): ?HydratorInterface
    {
        $hydrator = new StdClassHydrator();

        $hydrator->setNamingStrategy(new AliasNamingStrategy([
            'stream' => ['path'],
            'filePermission' => ['file_permission', 'file-permission'],
            'useLocking' => ['use_locking', 'use-locking'],
        ]));

        $boolHydratorStrategy = new BooleanStrategy(
            ['y', 'Y', 1],
            ['n', 'N', 0],
            false
        );

        $hydrator->addStrategy('bubble', $boolHydratorStrategy);
        $hydrator->addStrategy('useLocking', $boolHydratorStrategy);

        return $hydrator;
    }
}
