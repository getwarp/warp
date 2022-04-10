<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Mapper\Plugin\Blame;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Warp\Bridge\Cycle\Mapper\Plugin\QueueBeforeEvent;

/**
 * @template T of object
 */
final class BlamePlugin implements EventSubscriberInterface
{
    /**
     * @var BlameHandler<T>
     */
    private BlameHandler $handler;

    /**
     * @param BlameHandler<T> $handler
     */
    public function __construct(BlameHandler $handler)
    {
        $this->handler = $handler;
    }

    public function handle(QueueBeforeEvent $event): void
    {
        $this->handler->handle($event);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            QueueBeforeEvent::class => 'handle',
        ];
    }
}
