<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Monolog\Handler\HandlerInterface;

interface HandlerFactoryInterface
{
    /**
     * Returns list of supported handler types and/or its aliases.
     * @return string[]
     */
    public function supportedTypes(): array;

    /**
     * Creates monolog handler with given parameters.
     * @param array<string,mixed> $parameters
     * @param CompositeHandlerFactory $factory
     * @return HandlerInterface
     */
    public function make(array $parameters, CompositeHandlerFactory $factory): HandlerInterface;
}
