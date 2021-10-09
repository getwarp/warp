<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin\ForceEntityReference;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface;
use spaceonfire\Bridge\Cycle\EntityReference;
use spaceonfire\Bridge\Cycle\EntityReferenceFactory;
use spaceonfire\Bridge\Cycle\Mapper\Plugin\HydrateBeforeEvent;

final class ForceEntityReferenceHandler
{
    private const TO_ONE_RELATIONS = [
        Relation::EMBEDDED,
        Relation::HAS_ONE,
        Relation::BELONGS_TO,
        Relation::REFERS_TO,
        Relation::BELONGS_TO_MORPHED,
        Relation::MORPHED_HAS_ONE,
    ];

    private ORMInterface $orm;

    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
    }

    public function onHydrate(HydrateBeforeEvent $event): void
    {
        $data = $event->getData();
        $entity = $event->getEntity();
        $role = $this->orm->resolveRole($entity);

        $referenceFactory = null;
        $relations = $this->orm->getSchema()->define($role, SchemaInterface::RELATIONS);
        /**
         * @phpstan-var string $relation
         * @phpstan-var array<int,mixed> $relationSchema
         */
        foreach ($relations as $relation => $relationSchema) {
            if (!isset($data[$relation]) || !\is_object($data[$relation])) {
                continue;
            }

            if (!\in_array($relationSchema[Relation::TYPE], self::TO_ONE_RELATIONS, true)) {
                continue;
            }

            if ($data[$relation] instanceof EntityReference) {
                continue;
            }

            $referenceFactory ??= new EntityReferenceFactory();
            $data[$relation] = $referenceFactory->promisize($this->orm, $data[$relation]);
        }

        $event->replaceData($data);
    }
}
