<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin\BelongsToLink;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\TransactionInterface;
use spaceonfire\Bridge\Cycle\AbstractTestCase;
use spaceonfire\Bridge\Cycle\Fixtures\OrmCapsule;
use spaceonfire\Bridge\Cycle\Fixtures\Post;
use spaceonfire\Bridge\Cycle\Fixtures\User;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\DispatcherMapperPlugin;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\QueueAfterEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BelongsToLinkPluginTest extends AbstractTestCase
{
    private function makePlugin(OrmCapsule $capsule): DispatcherMapperPlugin
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new BelongsToLinkPlugin(new BelongsToLinkHandler($capsule->orm())));
        return new DispatcherMapperPlugin($eventDispatcher);
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testPluginLinkManaged(OrmCapsule $capsule): void
    {
        $mapperPlugin = $this->makePlugin($capsule);

        /** @var User $author */
        $author = $capsule->orm()->make(User::class, [
            'id' => '35a60006-c34a-4c0b-8e9d-7759f6d0c09b',
            'name' => 'Admin User',
        ], Node::MANAGED);

        $post = new Post(
            '0279d9bb-41e4-4fd0-ba05-87a2e112c7c2',
            'Yet another blog post',
            $author,
        );

        $command = $capsule->orm()->queueStore($post, TransactionInterface::MODE_ENTITY_ONLY);
        $node = $capsule->orm()->getHeap()->get($post);

        $mapperPlugin->dispatch(new QueueAfterEvent($post, $node, $node->getState(), $command));

        self::assertSame([
            'author_id' => '35a60006-c34a-4c0b-8e9d-7759f6d0c09b',
        ], $command->getContext());
        self::assertTrue($command->isReady());
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testPluginLinkScheduled(OrmCapsule $capsule): void
    {
        $mapperPlugin = $this->makePlugin($capsule);

        $author = new User('35a60006-c34a-4c0b-8e9d-7759f6d0c09b', 'Admin User');

        $post = new Post(
            '0279d9bb-41e4-4fd0-ba05-87a2e112c7c2',
            'Yet another blog post',
            $author,
        );

        $capsule->orm()->queueStore($author, TransactionInterface::MODE_ENTITY_ONLY);
        $command = $capsule->orm()->queueStore($post, TransactionInterface::MODE_ENTITY_ONLY);
        $node = $capsule->orm()->getHeap()->get($post);

        $mapperPlugin->dispatch(new QueueAfterEvent($post, $node, $node->getState(), $command));

        self::assertSame([
            'author_id' => '35a60006-c34a-4c0b-8e9d-7759f6d0c09b',
        ], $command->getContext());
        self::assertTrue($command->isReady());
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testPluginSkipped(OrmCapsule $capsule): void
    {
        $mapperPlugin = $this->makePlugin($capsule);

        $author = new User('35a60006-c34a-4c0b-8e9d-7759f6d0c09b', 'Admin User');

        $post = new Post(
            '0279d9bb-41e4-4fd0-ba05-87a2e112c7c2',
            'Yet another blog post',
            $author,
        );

        $command = $capsule->orm()->queueStore($post, TransactionInterface::MODE_ENTITY_ONLY);
        $node = $capsule->orm()->getHeap()->get($post);

        $mapperPlugin->dispatch(new QueueAfterEvent($post, $node, $node->getState(), $command));

        // context empty, command ready and SQL query will fail
        self::assertSame([], $command->getContext());
        self::assertTrue($command->isReady());
    }
}
