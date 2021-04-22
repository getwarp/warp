<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin\Blame;

use spaceonfire\Bridge\Cycle\Mapper\Plugin\QueueBeforeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
