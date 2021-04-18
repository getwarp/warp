<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\PsrHandler;
use Psr\Log\LoggerInterface;

abstract class AbstractPsrHandlerFactory extends AbstractHandlerFactory
{
    /**
     * @inheritDoc
     */
    public function make(array $parameters, CompositeHandlerFactory $factory): HandlerInterface
    {
        return new PsrHandler($this->makeLogger($parameters, $factory));
    }

    /**
     * @param array<string,mixed> $parameters
     * @param CompositeHandlerFactory $factory
     * @return LoggerInterface
     */
    abstract protected function makeLogger(array $parameters, CompositeHandlerFactory $factory): LoggerInterface;
}
