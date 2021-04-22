<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface;
use spaceonfire\DataSource\PropertyExtractorInterface;

/**
 * @internal
 */
final class CyclePropertyExtractor implements PropertyExtractorInterface
{
    private ORMInterface $orm;

    private string $role;

    public function __construct(ORMInterface $orm, string $role)
    {
        $this->orm = $orm;
        $this->role = $role;
    }

    public function extractValue(string $name, $value)
    {
        $name = $this->getPropertyExtractor()->extractName($name);
        [$relation, $property] = $this->splitName($name);

        if (null === $relation && $this->hasRelation($property)) {
            $relation = $property;
            $property = $this->getRelationPK($relation);
            $value = $this->extractRelationPK($relation, $value);
        }

        return $this->getPropertyExtractor($relation)->extractValue($property, $value);
    }

    public function extractName(string $name): string
    {
        $name = $this->getPropertyExtractor()->extractName($name);
        [$relation, $property] = $this->splitName($name);

        if (null === $relation && $this->hasRelation($property)) {
            $relation = $property;
            $property = $this->getRelationPK($relation);
        }

        $hydrator = $this->getPropertyExtractor($relation);
        return (null === $relation ? '' : $relation . '.') . $hydrator->extractName($property);
    }

    /**
     * @param string $name
     * @return array{string|null,string}
     */
    private function splitName(string $name): array
    {
        /**
         * @var string $left
         * @var string|null $right
         */
        [$left, $right] = \explode('.', $name, 2) + ['', null];

        return null === $right
            ? [null, $left]
            : [$left, $right];
    }

    private function getPropertyExtractor(?string $relation = null): PropertyExtractorInterface
    {
        if (null === $relation) {
            return HydratorMapper::getPropertyExtractor($this->orm->getMapper($this->role));
        }

        $relSchema = $this->orm->getSchema()->defineRelation($this->role, $relation);

        return new self($this->orm, $relSchema[Relation::TARGET]);
    }

    private function hasRelation(string $relation): bool
    {
        return \in_array($relation, $this->orm->getSchema()->getRelations($this->role), true);
    }

    private function getRelationPK(string $relation): string
    {
        $relSchema = $this->orm->getSchema()->defineRelation($this->role, $relation);
        return $this->getPK($relSchema[Relation::TARGET]);
    }

    private function getPK(string $role): string
    {
        return $this->orm->getSchema()->define($role, SchemaInterface::PRIMARY_KEY);
    }

    /**
     * @param string $relation
     * @param mixed $value
     * @return mixed
     */
    private function extractRelationPK(string $relation, $value)
    {
        $relSchema = $this->orm->getSchema()->defineRelation($this->role, $relation);
        $mapper = $this->orm->getMapper($relSchema[Relation::TARGET]);
        $extractedData = $mapper->extract($value);
        $pk = $this->getPK($relSchema[Relation::TARGET]);
        return $extractedData[$pk];
    }
}
