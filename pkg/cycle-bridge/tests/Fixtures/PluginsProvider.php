<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures;

use Psr\EventDispatcher\EventDispatcherInterface;
use spaceonfire\Bridge\Cycle\Mapper\MapperPluginInterface;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\Blame\BlamePlugin;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\DispatcherMapperPlugin;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\EntityEvents\EntityEventsHandler;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\EntityEvents\EntityEventsPlugin;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\GroupData\GroupDataPlugin;
use spaceonfire\Container\ServiceProvider\AbstractServiceProvider;
use spaceonfire\DataSource\Blame\BlameActorProviderInterface;
use spaceonfire\DataSource\Blame\NullBlameActorProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class PluginsProvider extends AbstractServiceProvider
{
    public function provides(): iterable
    {
        return [
            MapperPluginInterface::class,
            DispatcherMapperPlugin::class,
            EventDispatcherInterface::class,
            EventDispatcher::class,
            BlameActorProviderInterface::class,
        ];
    }

    public function register(): void
    {
        $this->getContainer()->define(BlameActorProviderInterface::class, NullBlameActorProvider::class);

        $this->getContainer()->define(EventDispatcherInterface::class, EventDispatcher::class);
        $this->getContainer()->define(EventDispatcher::class, [$this, 'makeEventDispatcher'], true);

        $this->getContainer()->define(MapperPluginInterface::class, DispatcherMapperPlugin::class);
        $this->getContainer()->define(DispatcherMapperPlugin::class, null, true);
    }

    public function makeEventDispatcher(): EventDispatcher
    {
        $dispatcher = new EventDispatcher();

        $dispatcher->addSubscriber($this->getContainer()->get(GroupDataPlugin::class));
        $dispatcher->addSubscriber($this->getContainer()->get(BlamePlugin::class));
        $dispatcher->addSubscriber(new EntityEventsPlugin(new EntityEventsHandler($dispatcher)));

        return $dispatcher;
    }
}
