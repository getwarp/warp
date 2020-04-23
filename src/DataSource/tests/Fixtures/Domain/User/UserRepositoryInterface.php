<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Domain\User;

use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\DataSource\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * @inheritDoc
     * @param User $entity
     */
    public function save($entity): void;

    /**
     * @inheritDoc
     * @param User $entity
     */
    public function remove($entity): void;

    /**
     * @inheritDoc
     * @param mixed $primary
     * @return User
     * @throws Exceptions\UserNotFoundException
     */
    public function findByPrimary($primary): User;

    /**
     * @inheritDoc
     * @return CollectionInterface|User[]
     */
    public function findAll(?CriteriaInterface $criteria = null): CollectionInterface;

    /**
     * @inheritDoc
     * @return User|null
     */
    public function findOne(?CriteriaInterface $criteria = null): ?User;
}
