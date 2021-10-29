<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Blame;

use PHPUnit\Framework\TestCase;
use spaceonfire\DataSource\EntityReferenceInterface;
use spaceonfire\DataSource\Fixtures\User;
use spaceonfire\ValueObject\Date\DateTimeImmutableValue;
use spaceonfire\ValueObject\Date\FrozenClock;
use spaceonfire\ValueObject\Date\SystemClock;

class BlameTest extends TestCase
{
    private function makeActor(): User
    {
        return new User('00000000-0000-0000-0000-000000000000', 'Admin');
    }

    private function makeReference(object $actor): EntityReferenceInterface
    {
        return new class($actor) implements EntityReferenceInterface {
            private object $entity;

            public function __construct(object $entity)
            {
                $this->entity = $entity;
            }

            public function getEntity(): object
            {
                return $this->entity;
            }

            public function getEntityOrNull(): ?object
            {
                return $this->getEntity();
            }

            public function equals(EntityReferenceInterface $other): bool
            {
                return $other instanceof self && $this->entity === $other->entity;
            }
        };
    }

    public function testNewEmpty(): void
    {
        $clock = new FrozenClock(SystemClock::fromUTC());
        $blame = Blame::new(User::class, null, null, $clock);
        $actor = $this->makeActor();
        $blame->touch($actor);

        self::assertTrue($blame->isNew());
        self::assertFalse($blame->isTouched());
        self::assertSame($clock->now(), $blame->getCreatedAt());
        self::assertSame($clock->now(), $blame->getUpdatedAt());
        self::assertSame($actor, $blame->getCreatedBy());
        self::assertSame($actor, $blame->getUpdatedBy());
    }

    public function testNewUpdated(): void
    {
        $clock = new FrozenClock(SystemClock::fromUTC());
        $createdAt = $clock->now();
        $actor = $this->makeActor();

        $blame = Blame::new(User::class, $createdAt, $actor, $clock);

        $clock->reset();
        $blame->touch($actor);

        self::assertFalse($blame->isNew());
        self::assertTrue($blame->isTouched());
        self::assertSame($createdAt, $blame->getCreatedAt());
        self::assertGreaterThan($createdAt, $blame->getUpdatedAt());
        self::assertSame($actor, $blame->getCreatedBy());
        self::assertSame($actor, $blame->getUpdatedBy());
    }

    public function testInitializeFromArray(): void
    {
        $actor = $this->makeActor();

        $data = [
            'createdAt' => DateTimeImmutableValue::from('2021-09-18 20:00:00'),
            'updatedAt' => DateTimeImmutableValue::from('2021-09-19 20:00:00'),
            'createdBy' => $this->makeReference($actor),
            'updatedBy' => $this->makeReference($actor),
        ];

        $blame = Blame::fromArray($data, User::class);

        self::assertSame($actor, $blame->getCreatedBy());
        self::assertSame($actor, $blame->getUpdatedBy());

        $array = $blame->toArray();

        self::assertArrayHasKey('createdAt', $array);
        self::assertArrayHasKey('updatedAt', $array);
        self::assertArrayHasKey('createdBy', $array);
        self::assertArrayHasKey('updatedBy', $array);
        self::assertSame($data['createdAt'], $array['createdAt']);
        self::assertSame($data['updatedAt'], $array['updatedAt']);
        self::assertSame($actor, $array['createdBy']);
        self::assertSame($actor, $array['updatedBy']);

        $array = $blame->toArray(['createdAt', 'createdBy']);
        self::assertArrayHasKey('createdAt', $array);
        self::assertArrayNotHasKey('updatedAt', $array);
        self::assertArrayHasKey('createdBy', $array);
        self::assertArrayNotHasKey('updatedBy', $array);
        self::assertSame($data['createdAt'], $array['createdAt']);
        self::assertSame($actor, $array['createdBy']);
    }

    public function testTouchWithNull(): void
    {
        $blame = Blame::new(null, DateTimeImmutableValue::from('2021-09-18 20:00:00'));

        $blame->touch();

        self::assertGreaterThan($blame->getCreatedAt(), $blame->getUpdatedAt());
        self::assertNull($blame->getCreatedBy());
        self::assertNull($blame->getUpdatedBy());

        $actor = $this->makeActor();
        $blame->touch($actor);

        self::assertNull($blame->getCreatedBy());
        self::assertSame($actor, $blame->getUpdatedBy());
    }

    public function testResolveActorFromInvalidReferenceCauseException(): void
    {
        $invalidActorRef = $this->makeReference((object)[]);

        $data = [
            'createdAt' => DateTimeImmutableValue::from('2021-09-18 20:00:00'),
            'updatedAt' => DateTimeImmutableValue::from('2021-09-19 20:00:00'),
            'createdBy' => $invalidActorRef,
            'updatedBy' => $invalidActorRef,
        ];

        $blame = Blame::fromArray($data, User::class);

        $this->expectException(\RuntimeException::class);
        $blame->getCreatedBy();
    }

    public function testCannotTouchUsingReference(): void
    {
        $actor = $this->makeActor();
        $actorRef = $this->makeReference($actor);

        $blame = Blame::new(User::class);

        $this->expectException(\InvalidArgumentException::class);
        $blame->touch($actorRef);
    }

    public function testCannotTouchUsingInvalidActor(): void
    {
        $invalidActor = (object)[];

        $blame = Blame::new(User::class);

        $this->expectException(\InvalidArgumentException::class);
        $blame->touch($invalidActor);
    }

    public function testImmutable(): void
    {
        $actor = $this->makeActor();

        $data = [
            'createdAt' => DateTimeImmutableValue::from('2021-09-18 20:00:00'),
            'updatedAt' => DateTimeImmutableValue::from('2021-09-19 20:00:00'),
            'createdBy' => $this->makeReference($actor),
            'updatedBy' => $this->makeReference($actor),
        ];

        $blame = new BlameImmutable(Blame::fromArray($data, User::class));

        self::assertFalse($blame->isNew());
        self::assertFalse($blame->isTouched());
        self::assertSame($data['createdAt'], $blame->getCreatedAt());
        self::assertSame($data['updatedAt'], $blame->getUpdatedAt());
        self::assertSame($actor, $blame->getCreatedBy());
        self::assertSame($actor, $blame->getUpdatedBy());

        $array = $blame->toArray();

        self::assertArrayHasKey('createdAt', $array);
        self::assertArrayHasKey('updatedAt', $array);
        self::assertArrayHasKey('createdBy', $array);
        self::assertArrayHasKey('updatedBy', $array);
        self::assertSame($data['createdAt'], $array['createdAt']);
        self::assertSame($data['updatedAt'], $array['updatedAt']);
        self::assertSame($actor, $array['createdBy']);
        self::assertSame($actor, $array['updatedBy']);
    }

    public function testNullActorProvider(): void
    {
        self::assertNull((new NullBlameActorProvider())->getActor());
    }
}
