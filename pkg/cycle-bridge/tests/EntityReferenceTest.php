<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle;

use Cycle\ORM\Promise\PromiseInterface;
use Cycle\ORM\Promise\Reference;
use PHPUnit\Framework\TestCase;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItem;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItemId;
use spaceonfire\DataSource\EntityNotFoundException;
use spaceonfire\DataSource\EntityReferenceInterface;

class EntityReferenceTest extends TestCase
{
    public function testFromEntity(): void
    {
        $entity = new TodoItem(TodoItemId::new('e4cd452f-5b98-4ffb-8c94-a4012e735860'), 'cycle-bridge');
        $ref = EntityReference::fromEntity($entity, $entity->getId());
        self::assertTrue($ref->__loaded());
        self::assertSame($entity, $ref->__resolve());
        self::assertSame(TodoItemId::ROLE, $ref->__role());
        self::assertSame([
            'id' => 'e4cd452f-5b98-4ffb-8c94-a4012e735860',
        ], $ref->__scope());
    }

    public function testFromEntityWithoutRef(): void
    {
        $entity = new TodoItem(TodoItemId::new('e4cd452f-5b98-4ffb-8c94-a4012e735860'), 'cycle-bridge');
        $ref = EntityReference::fromEntity($entity);

        self::assertTrue($ref->__loaded());
        self::assertSame($entity, $ref->__resolve());
        self::assertSame(TodoItem::class, $ref->__role());
        self::assertSame([], $ref->__scope());
    }

    public function testFromReference(): void
    {
        $entity = new TodoItem(TodoItemId::new('e4cd452f-5b98-4ffb-8c94-a4012e735860'), 'cycle-bridge');
        $ref = EntityReference::fromReference(TodoItem::class, $this->promisize($entity, TodoItemId::ROLE, [
            'id' => 'e4cd452f-5b98-4ffb-8c94-a4012e735860',
        ]));

        self::assertFalse($ref->__loaded());
        self::assertSame($entity, $ref->__resolve());
        self::assertTrue($ref->__loaded());
        self::assertSame(TodoItemId::ROLE, $ref->__role());
        self::assertSame([
            'id' => 'e4cd452f-5b98-4ffb-8c94-a4012e735860',
        ], $ref->__scope());
    }

    public function testFromReferenceNotFound(): void
    {
        $ref = EntityReference::fromReference(TodoItem::class, $this->promisize(null, TodoItemId::ROLE, [
            'id' => 'e4cd452f-5b98-4ffb-8c94-a4012e735860',
        ]));

        $this->expectException(EntityNotFoundException::class);
        $ref->getEntity();
    }

    public function testFromReferenceNotLoadable(): void
    {
        $ref = EntityReference::fromReference(TodoItem::class, new Reference(TodoItemId::ROLE, [
            'id' => 'e4cd452f-5b98-4ffb-8c94-a4012e735860',
        ]));

        $this->expectException(\RuntimeException::class);
        $ref->__resolve();
    }

    public function testEquals(): void
    {
        $entity = new TodoItem(TodoItemId::new('e4cd452f-5b98-4ffb-8c94-a4012e735860'), 'cycle-bridge');
        $promise = $this->promisize($entity, TodoItemId::ROLE, [
            'id' => 'e4cd452f-5b98-4ffb-8c94-a4012e735860',
        ]);

        $fromEntity = EntityReference::fromEntity($entity, $entity->getId());

        self::assertTrue($fromEntity->equals($fromEntity));

        $fromNotLoadedPromise = EntityReference::fromReference(TodoItem::class, clone $promise);

        self::assertTrue($fromEntity->equals($fromNotLoadedPromise));
        self::assertTrue($fromNotLoadedPromise->equals($fromEntity));

        $loadedPromise = clone $promise;
        $loadedPromise->__resolve();
        $fromLoadedPromise = EntityReference::fromReference(TodoItem::class, $loadedPromise);

        self::assertTrue($fromEntity->equals($fromLoadedPromise));
        self::assertTrue($fromNotLoadedPromise->equals($fromLoadedPromise));

        $otherEntity = new TodoItem(TodoItemId::new('be8db328-e99a-4da3-b289-5381025ca850'), 'cycle-bridge');
        $fromOtherEntity = EntityReference::fromEntity($otherEntity, $otherEntity->getId());

        $promiseOtherId = EntityReference::fromReference(TodoItem::class, $this->promisize($otherEntity, TodoItemId::ROLE, [
            'id' => 'be8db328-e99a-4da3-b289-5381025ca850',
        ]));

        self::assertFalse($fromEntity->equals($fromOtherEntity));
        self::assertFalse($fromEntity->equals($promiseOtherId));

        $promiseOtherRole = EntityReference::fromReference(\stdClass::class, $this->promisize((object)[], 'tag', [
            'id' => 'e4cd452f-5b98-4ffb-8c94-a4012e735860',
        ]));

        self::assertFalse($fromEntity->equals($promiseOtherRole));

        self::assertFalse($fromEntity->equals(new class implements EntityReferenceInterface {
            public function getEntity(): object
            {
                throw new \LogicException('not implemented');
            }

            public function getEntityOrNull(): ?object
            {
                return $this->getEntity();
            }

            public function equals(EntityReferenceInterface $other): bool
            {
                return false;
            }
        }));
    }

    private function promisize(?object $entity, string $role, array $scope): PromiseInterface
    {
        return new class($entity, $role, $scope) implements PromiseInterface {
            private string $role;
            private array $scope;
            private ?object $entity;
            private bool $loaded = false;

            public function __construct(?object $entity, string $role, array $scope)
            {
                $this->entity = $entity;
                $this->role = $role;
                $this->scope = $scope;
            }

            public function __loaded(): bool
            {
                return $this->loaded;
            }

            public function __resolve(): ?object
            {
                $this->loaded = true;
                return $this->entity;
            }

            public function __role(): string
            {
                return $this->role;
            }

            public function __scope(): array
            {
                return $this->scope;
            }
        };
    }
}
