<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin\Blame;

use Cycle\ORM\Heap\Node;
use spaceonfire\Bridge\Cycle\AbstractTestCase;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItem;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItemId;
use spaceonfire\Bridge\Cycle\Fixtures\User;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\DispatcherMapperPlugin;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\QueueBeforeEvent;
use spaceonfire\DataSource\Blame\BlameActorProviderInterface;
use spaceonfire\DataSource\Blame\NullBlameActorProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BlamePluginTest extends AbstractTestCase
{
    public function testPluginWithNull(): void
    {
        $entity = new TodoItem(null, 'FooBar');

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new BlamePlugin(new BlameHandler(new NullBlameActorProvider())));
        $mapperPlugin = new DispatcherMapperPlugin($eventDispatcher);

        $node = new Node(Node::NEW, [], TodoItemId::ROLE);

        $mapperPlugin->dispatch(new QueueBeforeEvent($entity, $node, $node->getState()));

        self::assertNull($entity->getBlame()->getCreatedBy());
        self::assertNull($entity->getBlame()->getUpdatedBy());
    }

    public function testPluginWithUser(): void
    {
        $user = new User('00000000-0000-0000-0000-000000000000', 'Admin');
        $actorProvider = new class($user) implements BlameActorProviderInterface {
            private object $actor;

            public function __construct(object $actor)
            {
                $this->actor = $actor;
            }

            public function getActor(): object
            {
                return $this->actor;
            }
        };
        $entity = new TodoItem(null, 'FooBar');

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new BlamePlugin(new BlameHandler($actorProvider)));
        $mapperPlugin = new DispatcherMapperPlugin($eventDispatcher);

        $node = new Node(Node::NEW, [], TodoItemId::ROLE);

        $mapperPlugin->dispatch(new QueueBeforeEvent($entity, $node, $node->getState()));

        self::assertSame($user, $entity->getBlame()->getCreatedBy());
        self::assertSame($user, $entity->getBlame()->getUpdatedBy());
    }

    public function testPluginHandlerSkipped(): void
    {
        $entity = (object)['id' => 99];

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new BlamePlugin(new BlameHandler(new NullBlameActorProvider())));
        $mapperPlugin = new DispatcherMapperPlugin($eventDispatcher);

        $node = new Node(Node::NEW, [], 'tag');

        $mapperPlugin->dispatch(new QueueBeforeEvent($entity, $node, $node->getState()));

        self::assertTrue(true);
    }
}
