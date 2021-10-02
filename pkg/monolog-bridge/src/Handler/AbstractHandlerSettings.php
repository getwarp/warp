<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Monolog\Handler;

use Laminas\Hydrator\Strategy\ClosureStrategy;
use Laminas\Hydrator\Strategy\StrategyInterface;
use Monolog\Formatter\FormatterInterface;
use Monolog\Logger;
use spaceonfire\Bridge\LaminasHydrator\HydrateConstructorTrait;
use spaceonfire\Bridge\LaminasHydrator\Strategy\BooleanStrategy;

/**
 * @phpstan-import-type Level from \Monolog\Logger
 * @phpstan-import-type LevelName from \Monolog\Logger
 */
abstract class AbstractHandlerSettings
{
    use HydrateConstructorTrait;

    /**
     * @var FormatterInterface|class-string<FormatterInterface>|null
     */
    public $formatter = null;

    /**
     * @var Level
     */
    public int $level = Logger::DEBUG;

    public bool $bubble = true;

    private static ?BooleanStrategy $booleanStrategy = null;

    private static ?StrategyInterface $levelStrategy = null;

    public function getFormatter(): ?FormatterInterface
    {
        if (null === $this->formatter) {
            return null;
        }

        if ($this->formatter instanceof FormatterInterface) {
            return $this->formatter;
        }

        return $this->formatter = new $this->formatter();
    }

    protected static function booleanStrategy(): BooleanStrategy
    {
        return self::$booleanStrategy ??= new BooleanStrategy(
            ['y', 'Y', 1],
            ['n', 'N', 0],
            false
        );
    }

    protected static function levelStrategy(): StrategyInterface
    {
        return self::$levelStrategy ??= new ClosureStrategy(
            null,
            static fn ($v) => Logger::toMonologLevel($v ?? Logger::DEBUG),
        );
    }
}
