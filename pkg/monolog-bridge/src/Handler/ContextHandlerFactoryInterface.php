<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Monolog\Handler\HandlerInterface;

interface ContextHandlerFactoryInterface
{
    /**
     * Creates monolog handler with given parameters.
     * @param string $context
     * @param array<string,mixed> $settings
     * @return HandlerInterface
     */
    public function make(string $context, array $settings): HandlerInterface;
}
