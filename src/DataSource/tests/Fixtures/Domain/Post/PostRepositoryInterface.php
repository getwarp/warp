<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Domain\Post;

use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\DataSource\RepositoryInterface;

interface PostRepositoryInterface extends RepositoryInterface
{
    /**
     * @inheritDoc
     * @param Post $entity
     */
    public function save($entity): void;

    /**
     * @inheritDoc
     * @param Post $entity
     */
    public function remove($entity): void;

    /**
     * @inheritDoc
     * @param mixed $primary
     * @return Post
     * @throws Exceptions\PostNotFoundException
     */
    public function findByPrimary($primary): Post;

    /**
     * @inheritDoc
     * @return CollectionInterface|Post[]
     */
    public function findAll(?CriteriaInterface $criteria = null): CollectionInterface;

    /**
     * @inheritDoc
     * @return Post|null
     */
    public function findOne(?CriteriaInterface $criteria = null): ?Post;
}
