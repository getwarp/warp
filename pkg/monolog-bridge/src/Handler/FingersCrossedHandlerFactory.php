<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Monolog\Handler;

use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\HandlerInterface;

final class FingersCrossedHandlerFactory implements HandlerFactoryInterface, WithContextFactoryInterface
{
    private ?ContextHandlerFactoryInterface $factory = null;

    public function supportedTypes(): array
    {
        return [
            FingersCrossedHandler::class,
            'fingersCrossed',
            'fingers_crossed',
            'fingers-crossed',
        ];
    }

    public function make(array $settings): HandlerInterface
    {
        $config = new FingersCrossedHandlerSettings($settings);

        if (null === $this->factory) {
            throw new \RuntimeException('No context factory given.');
        }

        $handler = new FingersCrossedHandler(
            $config->getHandler($this->factory),
            $config->activationStrategy,
            $config->bufferSize,
            $config->bubble,
            $config->stopBuffering,
            $config->passthruLevel,
        );

        if (null !== $formatter = $config->getFormatter()) {
            $handler->setFormatter($formatter);
        }

        return $handler;
    }

    public function withContextFactory(ContextHandlerFactoryInterface $factory): WithContextFactoryInterface
    {
        $clone = clone $this;
        $clone->factory = $factory;
        return $clone;
    }
}
