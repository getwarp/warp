<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Mapper;

use Cycle\ORM\Mapper\Mapper;
use Laminas\Hydrator\AbstractHydrator;
use spaceonfire\DataSource\MapperInterface;

class BasicCycleMapper extends Mapper implements MapperInterface
{
    /**
     * @var AbstractHydrator
     */
    protected $hydrator;

    /**
     * @inheritDoc
     */
    public function convertValueToDomain(string $fieldName, $storageValue)
    {
        return $this->hydrator->hydrateValue($fieldName, $storageValue);
    }

    /**
     * @inheritDoc
     */
    public function convertValueToStorage(string $fieldName, $domainValue)
    {
        return $this->hydrator->extractValue($fieldName, $domainValue);
    }

    /**
     * @inheritDoc
     */
    public function convertNameToDomain(string $fieldName): string
    {
        return $this->hydrator->hydrateName($fieldName);
    }

    /**
     * @inheritDoc
     */
    public function convertNameToStorage(string $fieldName): string
    {
        return $this->hydrator->extractName($fieldName);
    }
}
