<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

interface MapperInterface
{
    /**
     * Init empty entity object an return pre-filtered data (hydration will happen on a later stage).
     * Must return tuple [object entity, array entityData].
     * @param array $data
     * @return array
     */
    public function init(array $data): array;

    /**
     * Hydrate entity with dataset.
     * @param object $entity
     * @param array  $data
     * @return object
     */
    public function hydrate($entity, array $data);

    /**
     * Extract all values from the entity.
     * @param object $entity
     * @return array
     */
    public function extract($entity): array;

    /**
     * Converts field value from storage to domain
     * @param string $fieldName
     * @param mixed $storageValue
     * @return mixed
     */
    public function convertToDomain(string $fieldName, $storageValue);

    /**
     * Converts field value from domain to storage
     * @param string $fieldName
     * @param mixed $domainValue
     * @return mixed
     */
    public function convertToStorage(string $fieldName, $domainValue);
}
