<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ObjectPropertyHydrator;
use spaceonfire\Bridge\LaminasHydrator\NamingStrategy\AliasNamingStrategy;

final class StreamHandlerSettings extends AbstractHandlerSettings
{
    /**
     * @var string|resource
     */
    public $stream;

    public ?int $filePermission = null;

    public bool $useLocking = false;

    protected static function hydrator(): HydratorInterface
    {
        $hydrator = new ObjectPropertyHydrator();

        $hydrator->setNamingStrategy(new AliasNamingStrategy([
            'stream' => ['path'],
            'filePermission' => ['file_permission', 'file-permission'],
            'useLocking' => ['use_locking', 'use-locking'],
        ]));

        $hydrator->addStrategy('bubble', self::booleanStrategy());
        $hydrator->addStrategy('useLocking', self::booleanStrategy());
        $hydrator->addStrategy('level', self::levelStrategy());

        return $hydrator;
    }
}
