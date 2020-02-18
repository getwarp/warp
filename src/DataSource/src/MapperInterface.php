<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

interface MapperInterface
{
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
