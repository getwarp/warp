<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Promise\PromiseOne;
use Cycle\ORM\Promise\Reference;
use Cycle\ORM\PromiseFactoryInterface;
use Cycle\ORM\SchemaInterface;

final class EntityReferenceFactory implements PromiseFactoryInterface
{
    /**
     * @param ORMInterface $orm
     * @param string $role
     * @param array<array-key,mixed> $scope
     * @return EntityReference<object>
     */
    public function promise(ORMInterface $orm, string $role, array $scope): EntityReference
    {
        /** @phpstan-var class-string $class */
        $class = $orm->getSchema()->define($role, SchemaInterface::ENTITY) ?? \stdClass::class;
        $promise = new PromiseOne($orm, $role, $scope);
        return EntityReference::fromReference($class, $promise);
    }

    /**
     * @template T of object
     * @param ORMInterface $orm
     * @param T $entity
     * @return EntityReference<T>
     */
    public function promisize(ORMInterface $orm, object $entity): EntityReference
    {
        $role = $orm->resolveRole($entity);
        $node = $orm->getHeap()->get($entity);

        if (null === $node) {
            throw new \RuntimeException('Entity has not registered in ORM heap.');
        }

        $data = $node->getData();
        $pk = $orm->getSchema()->define($role, SchemaInterface::PRIMARY_KEY);
        $scope = [
            $pk => $data[$pk],
        ];
        return EntityReference::fromEntity($entity, new Reference($role, $scope));
    }
}
