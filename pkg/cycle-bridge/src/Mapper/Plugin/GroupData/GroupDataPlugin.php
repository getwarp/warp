<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Mapper\Plugin\GroupData;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Warp\Bridge\Cycle\Mapper\Plugin\ExtractAfterEvent;
use Warp\Bridge\Cycle\Mapper\Plugin\HydrateBeforeEvent;

final class GroupDataPlugin implements EventSubscriberInterface
{
    private GroupDataHandler $handler;

    public function __construct(GroupDataHandler $handler)
    {
        $this->handler = $handler;
    }

    public function onHydrate(HydrateBeforeEvent $event): void
    {
        $this->handler->onHydrate($event);
    }

    public function onExtract(ExtractAfterEvent $event): void
    {
        $this->handler->onExtract($event);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HydrateBeforeEvent::class => 'onHydrate',
            ExtractAfterEvent::class => 'onExtract',
        ];
    }
}
