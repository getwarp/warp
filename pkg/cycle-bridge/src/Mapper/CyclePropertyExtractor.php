<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface;
use spaceonfire\Bridge\Cycle\NodeHelper;
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
        return $this->getPropertyExtractor($relation)->extractValue($property, $value);
    }

    public function extractName(string $name): string
    {
        $name = $this->getPropertyExtractor()->extractName($name);
        [$relation, $property] = $this->splitName($name);
        $extractor = $this->getPropertyExtractor($relation);
        $field = $extractor->extractName($property);
        ($extractor instanceof self ? $extractor : $this)->assertFieldDefined($field);
        return (null === $relation ? '' : $relation . '.') . $field;
    }

    /**
     * @param string $name
     * @return array<array-key,mixed>|null
     */
    public function getRelationSchemaIfExists(string $name): ?array
    {
        $name = $this->getPropertyExtractor()->extractName($name);
        [$relation, $property] = $this->splitName($name);

        if (null !== $relation) {
            return $this->getRelationPropertyExtractor($relation)->getRelationSchemaIfExists($property);
        }

        if (!$this->hasRelation($property)) {
            return null;
        }

        return $this->orm->getSchema()->defineRelation($this->role, $property);
    }

    /**
     * @param string $key
     * @param object $entity
     * @return mixed
     */
    public function fetchKey(string $key, object $entity)
    {
        $node = $this->orm->getHeap()->get($entity);

        if (null === $node || !NodeHelper::nodePersisted($node)) {
            throw new \RuntimeException('Could not fetch key from entity, because it not managed by orm.');
        }

        return $node->getData()[$key];
    }

    public function getRole(object $entity): string
    {
        return $this->orm->resolveRole($entity);
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
        if (null !== $relation) {
            return $this->getRelationPropertyExtractor($relation);
        }

        return HydratorMapper::getPropertyExtractor(
            $this->orm->getSchema()->defines($this->role) ? $this->orm->getMapper($this->role) : null
        );
    }

    private function getRelationPropertyExtractor(string $relation): self
    {
        $relSchema = $this->orm->getSchema()->defineRelation($this->role, $relation);

        return new self($this->orm, $relSchema[Relation::TARGET]);
    }

    private function hasRelation(string $relation): bool
    {
        $schema = $this->orm->getSchema();
        return $schema->defines($this->role) && \in_array($relation, $schema->getRelations($this->role), true);
    }

    private function assertFieldDefined(string $field, ?string $role = null): void
    {
        if (\str_contains($field, '.')) {
            return;
        }

        $role ??= $this->role;
        $fields = $this->orm->getSchema()->define($role, SchemaInterface::COLUMNS);

        if (isset($fields[$field]) || \in_array($field, $fields, true)) {
            return;
        }

        throw new \InvalidArgumentException(\sprintf('Entity "%s" has not field "%s".', $role, $field));
    }
}
