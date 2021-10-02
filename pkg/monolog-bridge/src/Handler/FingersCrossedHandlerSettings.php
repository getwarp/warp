<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Monolog\Handler;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Monolog\Handler\FingersCrossed\ActivationStrategyInterface;
use Monolog\Handler\HandlerInterface;
use Psr\Log\LogLevel;
use spaceonfire\Bridge\LaminasHydrator\NamingStrategy\AliasNamingStrategy;
use spaceonfire\Bridge\LaminasHydrator\Strategy\NullableStrategy;

/**
 * @phpstan-import-type Level from \Monolog\Logger
 * @phpstan-import-type LevelName from \Monolog\Logger
 */
final class FingersCrossedHandlerSettings extends AbstractHandlerSettings
{
    /**
     * @var string|array<string,mixed>|HandlerInterface|callable():HandlerInterface
     */
    public $handler;

    /**
     * @var null|ActivationStrategyInterface|Level|LevelName|LogLevel::*
     */
    public $activationStrategy;

    public int $bufferSize = 0;

    public bool $stopBuffering = true;

    /**
     * @var Level|null
     */
    public ?int $passthruLevel = null;

    /**
     * @return HandlerInterface|callable():HandlerInterface
     */
    public function getHandler(ContextHandlerFactoryInterface $factory)
    {
        if ($this->handler instanceof HandlerInterface) {
            return $this->handler;
        }

        if (\is_array($this->handler) && !\is_callable($this->handler)) {
            return $factory->make(
                $this->handler['handler'] ?? $this->handler['driver'],
                $this->handler
            );
        }

        if (\is_string($this->handler)) {
            return $factory->make($this->handler, []);
        }

        $handler = $this->handler;

        if (!\is_callable($handler)) {
            throw new \InvalidArgumentException('Not supported handler format.');
        }

        /** @phpstan-var callable():HandlerInterface $handler */
        return $handler;
    }

    protected static function hydrator(): HydratorInterface
    {
        $hydrator = new ObjectPropertyHydrator();

        $hydrator->setNamingStrategy(new AliasNamingStrategy([
            'activationStrategy' => ['activation_strategy', 'activation-strategy'],
            'bufferSize' => ['buffer_size', 'buffer-size'],
            'stopBuffering' => ['stop_buffering', 'stop-buffering'],
            'passthruLevel' => ['passthru_level', 'passthru-level'],
        ]));

        $hydrator->addStrategy('bubble', self::booleanStrategy());
        $hydrator->addStrategy('stopBuffering', self::booleanStrategy());
        $hydrator->addStrategy('passthruLevel', new NullableStrategy(self::levelStrategy()));

        return $hydrator;
    }
}
