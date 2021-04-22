<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Promise\PromiseOne;
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
        $class = $orm->getSchema()->define($role, SchemaInterface::ENTITY) ?? \stdClass::class;
        $promise = new PromiseOne($orm, $role, $scope);
        return EntityReference::fromReference($class, $promise);
    }
}
