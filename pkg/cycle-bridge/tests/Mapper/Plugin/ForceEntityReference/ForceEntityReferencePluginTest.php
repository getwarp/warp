<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin\ForceEntityReference;

use spaceonfire\Bridge\Cycle\AbstractTestCase;
use spaceonfire\Bridge\Cycle\EntityReference;
use spaceonfire\Bridge\Cycle\Fixtures\OrmCapsule;
use spaceonfire\Bridge\Cycle\Fixtures\Post;
use spaceonfire\Bridge\Cycle\Fixtures\User;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\DispatcherMapperPlugin;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\HydrateBeforeEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ForceEntityReferencePluginTest extends AbstractTestCase
{
    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testPlugin(OrmCapsule $capsule): void
    {
        $author = $capsule->orm()->make($capsule->orm()->resolveRole(User::class), [
            'id' => '35a60006-c34a-4c0b-8e9d-7759f6d0c09b',
            'name' => 'Admin User',
        ]);

        $reflection = new \ReflectionClass(Post::class);
        $post = $reflection->newInstanceWithoutConstructor();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new ForceEntityReferencePlugin(new ForceEntityReferenceHandler($capsule->orm())));
        $mapperPlugin = new DispatcherMapperPlugin($eventDispatcher);

        /** @var HydrateBeforeEvent $event */
        $event = $mapperPlugin->dispatch(new HydrateBeforeEvent($post, [
            'id' => '0de0e289-28a7-4944-953a-079d09ac4865',
            'title' => 'Hello, World!',
            'author' => $author,
            'createdAt' => '2021-08-26 10:00:00',
        ]));

        $data = $event->getData();
        $authorRef = $data['author'];

        self::assertInstanceOf(EntityReference::class, $authorRef);
        self::assertTrue($authorRef->__loaded());
        self::assertSame($author, $authorRef->getEntity());
    }
}
