<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\DependencyInjection;

use spaceonfire\Container\ServiceProvider\AbstractServiceProvider;
use spaceonfire\MonologBridge\Handler;
use Symfony\Bridge\Monolog\Handler\MailerHandler;
use Symfony\Component\Console\Logger\ConsoleLogger;

final class HandlerFactoryServiceProvider extends AbstractServiceProvider
{
    public const DEFINITION_TAG = 'monolog.handler.factory';

    /**
     * @var bool
     */
    private $shareDefinitions;

    /**
     * HandlerFactoryServiceProvider constructor.
     * @param bool $shareDefinitions
     */
    public function __construct(bool $shareDefinitions = false)
    {
        $this->shareDefinitions = $shareDefinitions;
    }

    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        $provideList = $this->factories();

        $provideList[] = Handler\CompositeHandlerFactory::class;
        $provideList[] = self::DEFINITION_TAG;

        return $provideList;
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->add(
            Handler\CompositeHandlerFactory::class,
            [$this, 'makeCompositeHandlerFactory'],
            $this->shareDefinitions
        );

        foreach ($this->factories() as $handlerFactory) {
            $this->getContainer()
                ->add($handlerFactory, null, $this->shareDefinitions)
                ->addTag(self::DEFINITION_TAG);
        }
    }

    public function makeCompositeHandlerFactory(): Handler\CompositeHandlerFactory
    {
        return new Handler\CompositeHandlerFactory(
            $this->getContainer()->getTagged(self::DEFINITION_TAG)
        );
    }

    /**
     * @return array<string|class-string>
     */
    private function factories(): array
    {
        return array_keys(array_filter([
            Handler\StreamHandlerFactory::class => true,
            Handler\FingersCrossedHandlerFactory::class => true,
            Handler\ConsoleHandlerFactory::class => class_exists(ConsoleLogger::class),
            Handler\MailerHandlerFactory::class => class_exists(MailerHandler::class),
        ]));
    }
}
