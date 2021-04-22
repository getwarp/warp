<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper;

use Cycle\ORM\Command\Branch\ContextSequence;
use Cycle\ORM\Command\Branch\Sequence;
use Cycle\ORM\Command\Database\Delete;
use Cycle\ORM\Command\Database\Update;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Schema;
use spaceonfire\Bridge\Cycle\AbstractTestCase;
use spaceonfire\Bridge\Cycle\Fixtures\OrmCapsule;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\ExtractAfterEvent;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\ExtractBeforeEvent;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\HydrateAfterEvent;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\HydrateBeforeEvent;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\NoopMapperPlugin;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\QueueAfterEvent;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\QueueBeforeEvent;
use spaceonfire\ValueObject\Date\DateTimeImmutableValue;

class MapperPluginTest extends AbstractTestCase
{
    public function testNoopPlugin(): void
    {
        $createdAt = DateTimeImmutableValue::from('2021-09-18 20:00:00');
        $updatedAt = DateTimeImmutableValue::from('2021-09-19 20:00:00');

        $event = new HydrateBeforeEvent((object)[], [
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'createdBy' => null,
            'updatedBy' => null,
        ]);
        /** @var HydrateBeforeEvent $event */
        $event = (new NoopMapperPlugin())->dispatch($event);

        self::assertSame([
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'createdBy' => null,
            'updatedBy' => null,
        ], $event->getData());
    }

    public function testExtractAfterEvent(): void
    {
        $entity = (object)[];
        $event = new ExtractAfterEvent($entity, [
            'id' => 42,
            'value' => 'foo',
        ]);

        self::assertSame($entity, $event->getEntity());
        self::assertSame([
            'id' => 42,
            'value' => 'foo',
        ], $event->getData());

        $event->replaceData([
            'id' => 42,
            'value' => 'bar',
        ]);
        self::assertSame([
            'id' => 42,
            'value' => 'bar',
        ], $event->getData());
    }

    public function testExtractBeforeEvent(): void
    {
        $entity = (object)[];
        $event = new ExtractBeforeEvent($entity);
        self::assertSame($entity, $event->getEntity());
    }

    public function testHydrateBeforeEvent(): void
    {
        $entity = (object)[];
        $event = new HydrateBeforeEvent($entity, [
            'id' => 42,
            'value' => 'foo',
        ]);

        self::assertSame($entity, $event->getEntity());
        self::assertSame([
            'id' => 42,
            'value' => 'foo',
        ], $event->getData());

        $event->replaceData([
            'id' => 42,
            'value' => 'bar',
        ]);
        self::assertSame([
            'id' => 42,
            'value' => 'bar',
        ], $event->getData());
    }

    public function testHydrateAfterEvent(): void
    {
        $entity = (object)[];
        $event = new HydrateAfterEvent($entity, [
            'id' => 42,
            'value' => 'foo',
        ]);
        self::assertSame($entity, $event->getEntity());
        self::assertSame([
            'id' => 42,
            'value' => 'foo',
        ], $event->getData());
    }

    public function testQueueBeforeEvent(): void
    {
        $entity = (object)[];
        $node = new Node(Node::NEW, [], 'tag');

        $event = new QueueBeforeEvent($entity, $node, $node->getState());

        self::assertSame($entity, $event->getEntity());
        self::assertSame($node, $event->getNode());
        self::assertSame($node->getState(), $event->getState());
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testQueueAfterEvent(OrmCapsule $capsule): void
    {
        $entity = (object)[];
        $node = new Node(Node::NEW, [], 'tag');

        $event = new QueueAfterEvent(
            $entity,
            $node,
            $node->getState(),
            $delete = new Delete(
                $capsule->database(),
                $capsule->orm()->getSchema()->define($node->getRole(), Schema::TABLE),
                [],
            )
        );

        self::assertSame($entity, $event->getEntity());
        self::assertSame($node, $event->getNode());
        self::assertSame($node->getState(), $event->getState());
        self::assertSame($delete, $event->getCommand());

        $seq = $event->makeSequence($delete);
        self::assertInstanceOf(Sequence::class, $seq);
        self::assertContains($delete, $seq->getCommands());

        $event->replaceCommand($seq);

        self::assertSame($seq, $event->getCommand());
        self::assertSame($seq, $event->makeSequence($seq));
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testQueueAfterEventWithContext(OrmCapsule $capsule): void
    {
        $entity = (object)[];
        $node = new Node(Node::NEW, [], 'tag');

        $event = new QueueAfterEvent(
            $entity,
            $node,
            $node->getState(),
            $update = new Update(
                $capsule->database(),
                $capsule->orm()->getSchema()->define($node->getRole(), Schema::TABLE),
                [],
            )
        );

        self::assertSame($update, $event->getCommand());

        $seq = $event->makeSequence($update);
        self::assertInstanceOf(ContextSequence::class, $seq);
        self::assertSame($update, $seq->getPrimary());

        $event->replaceCommand($seq);

        self::assertSame($seq, $event->getCommand());
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testQueueAfterEventReplaceContextCommandWithNoContext(OrmCapsule $capsule): void
    {
        $entity = (object)[];
        $node = new Node(Node::NEW, [], 'tag');

        $event = new QueueAfterEvent(
            $entity,
            $node,
            $node->getState(),
            $update = new Update(
                $capsule->database(),
                $capsule->orm()->getSchema()->define($node->getRole(), Schema::TABLE),
                [],
            )
        );

        $seq = new Sequence();
        $seq->addCommand($update);

        $this->expectException(\InvalidArgumentException::class);
        $event->replaceCommand($seq);
    }
}
