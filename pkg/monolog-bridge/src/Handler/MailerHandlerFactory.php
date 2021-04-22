<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Monolog\Handler\HandlerInterface;
use Symfony\Bridge\Monolog\Handler\MailerHandler;
use Symfony\Component\Mailer\MailerInterface;

final class MailerHandlerFactory implements HandlerFactoryInterface
{
    private ?MailerInterface $mailer;

    public function __construct(?MailerInterface $mailer = null)
    {
        $this->mailer = $mailer;
    }

    public function supportedTypes(): array
    {
        if (!\class_exists(MailerHandler::class)) {
            return [];
        }

        return [
            'mailer',
            MailerHandler::class,
        ];
    }

    public function make(array $settings): HandlerInterface
    {
        $config = new MailerHandlerSettings($settings);

        $mailer = $config->mailer ?? $this->mailer;
        \assert(null !== $mailer);

        $handler = new MailerHandler(
            $mailer,
            $config->getMessageTemplate(),
            $config->level,
            $config->bubble,
        );

        if (null !== $formatter = $config->getFormatter()) {
            $handler->setFormatter($formatter);
        }

        return $handler;
    }
}
