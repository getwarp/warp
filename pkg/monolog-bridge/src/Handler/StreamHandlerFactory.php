<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;

final class StreamHandlerFactory implements HandlerFactoryInterface
{
    public function supportedTypes(): array
    {
        return [
            'stream',
            StreamHandler::class,
        ];
    }

    public function make(array $settings): HandlerInterface
    {
        $config = new StreamHandlerSettings($settings);

        $handler = new StreamHandler(
            $config->stream,
            $config->level,
            $config->bubble,
            $config->filePermission,
            $config->useLocking,
        );

        if (null !== $formatter = $config->getFormatter()) {
            $handler->setFormatter($formatter);
        }

        return $handler;
    }
}
