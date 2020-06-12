<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Infrastructure\Mapper;

use spaceonfire\DataSource\MapperInterface;

/**
 * Class StubMapper
 * @package spaceonfire\DataSource\Fixtures\Infrastructure\Mapper
 * @codeCoverageIgnore
 */
class StubMapper implements MapperInterface
{
    /**
     * @inheritDoc
     */
    public function init(array $data): array
    {
        return [new \stdClass(), $data];
    }

    /**
     * @inheritDoc
     */
    public function hydrate($entity, array $data)
    {
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function extract($entity): array
    {
        return get_object_vars($entity);
    }

    /**
     * @inheritDoc
     */
    public function convertValueToDomain(string $fieldName, $storageValue)
    {
        return $storageValue;
    }

    /**
     * @inheritDoc
     */
    public function convertValueToStorage(string $fieldName, $domainValue)
    {
        return $domainValue;
    }

    /**
     * @inheritDoc
     */
    public function convertNameToDomain(string $fieldName): string
    {
        return $fieldName;
    }

    /**
     * @inheritDoc
     */
    public function convertNameToStorage(string $fieldName): string
    {
        return $fieldName;
    }
}
