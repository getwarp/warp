<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Monolog\DependencyInjection;

use spaceonfire\Bridge\Monolog\Handler;
use spaceonfire\Container\Factory\DefinitionTag;
use spaceonfire\Container\ServiceProvider\AbstractServiceProvider;
use Symfony\Bridge\Monolog\Handler\MailerHandler;
use Symfony\Component\Console\Logger\ConsoleLogger;

final class HandlerFactoryServiceProvider extends AbstractServiceProvider
{
    private bool $shareDefinitions;

    public function __construct(bool $shareDefinitions = false)
    {
        $this->shareDefinitions = $shareDefinitions;
    }

    public function provides(): iterable
    {
        yield from $this->factories();
        yield Handler\ContextHandlerFactoryInterface::class;
        yield Handler\HandlerFactoryAggregate::class;
        yield DefinitionTag::MONOLOG_HANDLER_FACTORY;
    }

    public function register(): void
    {
        $this->getContainer()->define(
            Handler\ContextHandlerFactoryInterface::class,
            Handler\HandlerFactoryAggregate::class,
        );
        $this->getContainer()->define(
            Handler\HandlerFactoryAggregate::class,
            [$this, 'makeCompositeHandlerFactory'],
            $this->shareDefinitions,
        );

        foreach ($this->factories() as $handlerFactory) {
            $this->getContainer()
                ->define($handlerFactory, null, $this->shareDefinitions)
                ->addTag(DefinitionTag::MONOLOG_HANDLER_FACTORY);
        }
    }

    public function makeCompositeHandlerFactory(): Handler\HandlerFactoryAggregate
    {
        $aggregate = new Handler\HandlerFactoryAggregate();

        foreach ($this->getContainer()->getTagged(DefinitionTag::MONOLOG_HANDLER_FACTORY) as $factory) {
            if (!$factory instanceof Handler\HandlerFactoryInterface) {
                continue;
            }

            $aggregate->add($factory);
        }

        return $aggregate;
    }

    /**
     * @return \Generator<class-string<Handler\HandlerFactoryInterface>>
     */
    private function factories(): \Generator
    {
        yield Handler\StreamHandlerFactory::class;
        yield Handler\FingersCrossedHandlerFactory::class;

        if (\class_exists(ConsoleLogger::class)) {
            yield Handler\ConsoleHandlerFactory::class;
        }
        if (\class_exists(MailerHandler::class)) {
            yield Handler\MailerHandlerFactory::class;
        }
    }
}
