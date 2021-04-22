<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle;

use Cycle\ORM\Promise\PromiseInterface;
use Cycle\ORM\Promise\Reference;
use PHPUnit\Framework\TestCase;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItem;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItemId;

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

        $this->expectException(\RuntimeException::class);
        $ref->__scope();
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

    public function testFromReferenceNotLoadable(): void
    {
        $entity = new TodoItem(TodoItemId::new('e4cd452f-5b98-4ffb-8c94-a4012e735860'), 'cycle-bridge');
        $ref = EntityReference::fromReference(TodoItem::class, new Reference(TodoItemId::ROLE, [
            'id' => 'e4cd452f-5b98-4ffb-8c94-a4012e735860',
        ]));

        $this->expectException(\RuntimeException::class);
        $ref->__resolve();
    }

    private function promisize(object $entity, string $role, array $scope): PromiseInterface
    {
        return new class($entity, $role, $scope) implements PromiseInterface {
            private string $role;
            private array $scope;
            private object $entity;
            private bool $loaded = false;

            public function __construct(object $entity, string $role, array $scope)
            {
                $this->entity = $entity;
                $this->role = $role;
                $this->scope = $scope;
            }

            public function __loaded(): bool
            {
                return $this->loaded;
            }

            public function __resolve(): object
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
