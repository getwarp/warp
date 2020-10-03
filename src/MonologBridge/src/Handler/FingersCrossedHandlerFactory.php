<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Laminas\Hydrator\HydratorInterface;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\HandlerInterface;
use spaceonfire\LaminasHydratorBridge\NamingStrategy\AliasNamingStrategy;
use spaceonfire\LaminasHydratorBridge\StdClassHydrator;
use spaceonfire\LaminasHydratorBridge\Strategy\BooleanStrategy;

final class FingersCrossedHandlerFactory extends AbstractHandlerFactory
{
    /**
     * @inheritDoc
     */
    public function supportedTypes(): array
    {
        return [
            FingersCrossedHandler::class,
            'fingersCrossed',
            'fingers_crossed',
            'fingers-crossed',
        ];
    }

    /**
     * @inheritDoc
     */
    public function make(array $parameters, CompositeHandlerFactory $factory): HandlerInterface
    {
        $parametersHydrated = $this->hydrateParameters($parameters);

        if ($parametersHydrated->handler instanceof HandlerInterface || is_callable($parametersHydrated->handler)) {
            $handler = $parametersHydrated->handler;
        } elseif (is_array($parametersHydrated->handler)) {
            $handler = $factory->make(
                $parametersHydrated->handler['handler'] ?? $parametersHydrated->handler['driver'],
                $parametersHydrated->handler
            );
        } elseif (is_string($parametersHydrated->handler)) {
            $handler = $factory->make($parametersHydrated->handler, []);
        } else {
            throw new \InvalidArgumentException('Not supported handler format.');
        }

        return new FingersCrossedHandler(
            $handler,
            $parametersHydrated->activationStrategy ?? null,
            $parametersHydrated->bufferSize ?? 0,
            $parametersHydrated->bubble ?? true,
            $parametersHydrated->stopBuffering ?? true,
            $parametersHydrated->passthruLevel ?? null
        );
    }

    protected function getParametersHydrator(): ?HydratorInterface
    {
        $hydrator = new StdClassHydrator();

        // $activationStrategy = null, int $bufferSize = 0, bool $bubble = true, bool $stopBuffering = true, $passthruLevel = null
        $hydrator->setNamingStrategy(new AliasNamingStrategy([
            'activationStrategy' => ['activation_strategy', 'activation-strategy'],
            'bufferSize' => ['buffer_size', 'buffer-size'],
            'stopBuffering' => ['stop_buffering', 'stop-buffering'],
            'passthruLevel' => ['passthru_level', 'passthru-level'],
        ]));

        $boolHydratorStrategy = new BooleanStrategy(
            ['y', 'Y', 1],
            ['n', 'N', 0],
            false
        );

        $hydrator->addStrategy('bubble', $boolHydratorStrategy);
        $hydrator->addStrategy('stopBuffering', $boolHydratorStrategy);

        return $hydrator;
    }
}
