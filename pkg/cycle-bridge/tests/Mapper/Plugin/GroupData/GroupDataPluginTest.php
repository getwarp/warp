<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Mapper\Plugin\GroupData;

use Warp\Bridge\Cycle\AbstractTestCase;
use Warp\Bridge\Cycle\Fixtures\OrmCapsule;
use Warp\Bridge\Cycle\Fixtures\Todo\TodoItem;
use Warp\Bridge\Cycle\Mapper\Plugin\DispatcherMapperPlugin;
use Warp\Bridge\Cycle\Mapper\Plugin\ExtractAfterEvent;
use Warp\Bridge\Cycle\Mapper\Plugin\HydrateBeforeEvent;
use Warp\Clock\DateTimeImmutableValue;
use Symfony\Component\EventDispatcher\EventDispatcher;

class GroupDataPluginTest extends AbstractTestCase
{
    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testPlugin(OrmCapsule $capsule): void
    {
        $reflection = new \ReflectionClass(TodoItem::class);
        $entity = $reflection->newInstanceWithoutConstructor();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new GroupDataPlugin(new GroupDataHandler($capsule->orm())));
        $mapperPlugin = new DispatcherMapperPlugin($eventDispatcher);

        $createdAt = DateTimeImmutableValue::from('2021-09-18 20:00:00');
        $updatedAt = DateTimeImmutableValue::from('2021-09-19 20:00:00');

        /** @var HydrateBeforeEvent $event */
        $event = $mapperPlugin->dispatch(new HydrateBeforeEvent($entity, [
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'createdBy' => null,
            'updatedBy' => null,
        ]));

        self::assertArrayHasKey('blame', $event->getData());
        self::assertSame([
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'createdBy' => null,
            'updatedBy' => null,
        ], $event->getData()['blame']);

        /** @var HydrateBeforeEvent $event */
        $event = $mapperPlugin->dispatch(new ExtractAfterEvent($entity, [
            'blame' => [
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
                'createdBy' => null,
                'updatedBy' => null,
            ],
        ]));

        self::assertSame([
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'createdBy' => null,
            'updatedBy' => null,
        ], $event->getData());
    }
}
