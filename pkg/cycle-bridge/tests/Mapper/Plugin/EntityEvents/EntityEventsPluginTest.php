<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin\EntityEvents;

use Cycle\ORM\Command\Branch\ContextSequence;
use Cycle\ORM\Command\Branch\Sequence;
use Cycle\ORM\Command\Database\Delete;
use Cycle\ORM\Command\Database\Insert;
use Cycle\ORM\Command\Database\Update;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Schema;
use spaceonfire\Bridge\Cycle\AbstractTestCase;
use spaceonfire\Bridge\Cycle\Fixtures\OrmCapsule;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItem;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItemCreatedEvent;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItemDoneEvent;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItemId;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\DispatcherMapperPlugin;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\QueueAfterEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EntityEventsPluginTest extends AbstractTestCase
{
    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testPluginWithInsert(OrmCapsule $capsule): void
    {
        $entity = new TodoItem(null, 'FooBar');
        $entity->markDone();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new EntityEventsPlugin(new EntityEventsHandler($eventDispatcher)));
        $mapperPlugin = new DispatcherMapperPlugin($eventDispatcher);

        $node = new Node(Node::NEW, [], TodoItemId::ROLE);

        /** @var QueueAfterEvent $event */
        $event = $mapperPlugin->dispatch(new QueueAfterEvent(
            $entity,
            $node,
            $node->getState(),
            $insert = new Insert(
                $capsule->database(),
                $capsule->orm()->getSchema()->define($node->getRole(), Schema::TABLE),
                [],
                'id',
            )
        ));

        $command = $event->getCommand();

        self::assertInstanceOf(ContextSequence::class, $command);
        self::assertSame($insert, $command->getPrimary());
        $commands = [...$command->getIterator()];
        $dispatchCommand = $commands[1];
        self::assertInstanceOf(DispatchEventsCommand::class, $dispatchCommand);
        self::assertTrue($dispatchCommand->isReady());

        $eventDispatcher->addSubscriber(
            $subscription = new class implements EventSubscriberInterface {
                public array $events = [];

                public function handle(object $event): void
                {
                    $this->events[] = $event;
                }

                public static function getSubscribedEvents(): array
                {
                    return [
                        TodoItemCreatedEvent::class => 'handle',
                        TodoItemDoneEvent::class => 'handle',
                    ];
                }
            }
        );

        $dispatchCommand->complete();

        self::assertCount(2, $subscription->events);
        self::assertInstanceOf(TodoItemCreatedEvent::class, $subscription->events[0]);
        self::assertInstanceOf(TodoItemDoneEvent::class, $subscription->events[1]);
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testPluginWithUpdate(OrmCapsule $capsule): void
    {
        $entity = new TodoItem(null, 'FooBar');
        $entity->releaseEvents();
        $entity->markDone();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new EntityEventsPlugin(new EntityEventsHandler($eventDispatcher)));
        $mapperPlugin = new DispatcherMapperPlugin($eventDispatcher);

        $node = new Node(Node::SCHEDULED_UPDATE, [], TodoItemId::ROLE);

        /** @var QueueAfterEvent $event */
        $event = $mapperPlugin->dispatch(new QueueAfterEvent(
            $entity,
            $node,
            $node->getState(),
            $update = new Update(
                $capsule->database(),
                $capsule->orm()->getSchema()->define($node->getRole(), Schema::TABLE),
                [],
                $entity->getId()->__scope(),
            )
        ));

        $command = $event->getCommand();

        self::assertInstanceOf(ContextSequence::class, $command);
        self::assertSame($update, $command->getPrimary());
        $commands = [...$command->getIterator()];
        $dispatchCommand = $commands[1];
        self::assertInstanceOf(DispatchEventsCommand::class, $dispatchCommand);
        self::assertTrue($dispatchCommand->isReady());

        $eventDispatcher->addSubscriber(
            $subscription = new class implements EventSubscriberInterface {
                public array $events = [];

                public function handle(object $event): void
                {
                    $this->events[] = $event;
                }

                public static function getSubscribedEvents(): array
                {
                    return [
                        TodoItemCreatedEvent::class => 'handle',
                        TodoItemDoneEvent::class => 'handle',
                    ];
                }
            }
        );

        $dispatchCommand->complete();

        self::assertCount(1, $subscription->events);
        self::assertInstanceOf(TodoItemDoneEvent::class, $subscription->events[0]);
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testPluginWithDelete(OrmCapsule $capsule): void
    {
        $entity = new TodoItem(null, 'FooBar');
        $entity->releaseEvents();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new EntityEventsPlugin(new EntityEventsHandler($eventDispatcher)));
        $mapperPlugin = new DispatcherMapperPlugin($eventDispatcher);

        $node = new Node(Node::SCHEDULED_DELETE, [], TodoItemId::ROLE);

        /** @var QueueAfterEvent $event */
        $event = $mapperPlugin->dispatch(new QueueAfterEvent(
            $entity,
            $node,
            $node->getState(),
            $delete = new Delete(
                $capsule->database(),
                $capsule->orm()->getSchema()->define($node->getRole(), Schema::TABLE),
                $entity->getId()->__scope(),
            )
        ));

        $command = $event->getCommand();

        self::assertInstanceOf(Sequence::class, $command);
        $commands = [...$command->getIterator()];
        self::assertSame($delete, $commands[0]);
        $dispatchCommand = $commands[1];
        self::assertInstanceOf(DispatchEventsCommand::class, $dispatchCommand);
        self::assertTrue($dispatchCommand->isReady());

        $eventDispatcher->addSubscriber(
            $subscription = new class implements EventSubscriberInterface {
                public array $events = [];

                public function handle(object $event): void
                {
                    $this->events[] = $event;
                }

                public static function getSubscribedEvents(): array
                {
                    return [
                        TodoItemCreatedEvent::class => 'handle',
                        TodoItemDoneEvent::class => 'handle',
                    ];
                }
            }
        );

        $dispatchCommand->complete();

        self::assertCount(0, $subscription->events);
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testPluginHandlerSkipped(OrmCapsule $capsule): void
    {
        $entity = (object)['id' => 99];

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new EntityEventsPlugin(new EntityEventsHandler($eventDispatcher)));
        $mapperPlugin = new DispatcherMapperPlugin($eventDispatcher);

        $node = new Node(Node::NEW, [], 'tag');

        /** @var QueueAfterEvent $event */
        $event = $mapperPlugin->dispatch(new QueueAfterEvent(
            $entity,
            $node,
            $node->getState(),
            $insert = new Insert(
                $capsule->database(),
                $capsule->orm()->getSchema()->define($node->getRole(), Schema::TABLE),
                [],
                'id',
            )
        ));

        $command = $event->getCommand();

        self::assertSame($insert, $command);
    }
}
