<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\DependencyInjection;

use PHPUnit\Framework\TestCase;
use spaceonfire\Container\DefinitionContainer;
use spaceonfire\Container\Factory\DefinitionTag;
use spaceonfire\MonologBridge\Handler;

class HandlerFactoryServiceProviderTest extends TestCase
{
    public function testProvider(): void
    {
        $container = new DefinitionContainer();

        $provider = new HandlerFactoryServiceProvider(true);

        $container->addServiceProvider($provider);

        self::assertTrue($container->has(Handler\StreamHandlerFactory::class));
        self::assertTrue($container->has(Handler\FingersCrossedHandlerFactory::class));
        self::assertTrue($container->has(Handler\ConsoleHandlerFactory::class));
        self::assertTrue($container->has(Handler\MailerHandlerFactory::class));
        self::assertTrue($container->has(Handler\ContextHandlerFactoryInterface::class));
        self::assertTrue($container->has(Handler\HandlerFactoryAggregate::class));
        self::assertTrue($container->hasTagged(DefinitionTag::MONOLOG_HANDLER_FACTORY));

        self::assertInstanceOf(Handler\HandlerFactoryAggregate::class, $container->get(Handler\ContextHandlerFactoryInterface::class));
    }
}
