<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use spaceonfire\Collection\CollectionInterface;
use spaceonfire\DataSource\Criteria\Criteria;

interface RepositoryInterface
{
    /**
     * Persist entity in storage
     * @param EntityInterface $entity
     */
    public function save($entity): void;

    /**
     * Removes entity from storage
     * @param EntityInterface $entity
     */
    public function remove($entity): void;

    /**
     * Returns entity by it's identity
     * @param mixed $id
     * @return EntityInterface
     * @throws Exceptions\NotFoundException
     */
    public function getById($id);

    /**
     * Returns entity collection matching provided criteria
     * @param Criteria $criteria
     * @return CollectionInterface
     */
    public function getList(Criteria $criteria): CollectionInterface;

    /**
     * Returns query for entity
     * @return QueryInterface
     */
    public function query(): QueryInterface;

    /**
     * Returns mapper for repository's entity
     * @return MapperInterface
     */
    public function getMapper(): MapperInterface;
}
