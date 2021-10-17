<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Schema;

use Cycle\Schema\Registry;

/**
 * @phpstan-import-type EntityShape from EntityDto
 */
final class ArraySchemaRegistryFactory extends AbstractRegistryFactory
{
    /**
     * @var EntityShape[]
     */
    private array $entities = [];

    /**
     * @param EntityShape $entity
     * @param EntityShape ...$entities
     */
    public function loadEntity(array $entity, array ...$entities): void
    {
        foreach ([$entity, ...$entities] as $e) {
            $this->entities[] = $e;
        }
    }

    public function make(): Registry
    {
        $registry = new Registry($this->dbal);

        foreach ($this->entities as $entity) {
            $e = EntityDto::makeSchema($entity);
            $this->autocompleteEntity($e);
            $registry->register($e);
            $registry->linkTable($e, $entity[EntityDto::DATABASE] ?? null, $entity[EntityDto::TABLE]);

            foreach (EntityDto::makeChildren($entity) as $child) {
                $registry->registerChild($e, $child);
            }
        }

        return $registry;
    }
}
