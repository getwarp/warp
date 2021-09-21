<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\Post;

use spaceonfire\Collection\Collection;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\DataSource\EntityInterface;
use spaceonfire\DataSource\Fixtures\Domain\Post\Exceptions\PostNotFoundException;
use spaceonfire\DataSource\Fixtures\Domain\Post\Post;
use spaceonfire\DataSource\Fixtures\Domain\Post\PostRepositoryInterface;
use spaceonfire\DataSource\Fixtures\Infrastructure\Mapper\StubMapper;
use spaceonfire\DataSource\MapperInterface;
use Webmozart\Assert\Assert;

class InMemoryPostRepository implements PostRepositoryInterface
{
    /**
     * @var Post[]
     */
    private $storage;
    /**
     * @var MapperInterface
     */
    private $mapper;

    /**
     * InMemoryPostRepository constructor.
     * @param Post[] $posts
     */
    public function __construct(array $posts)
    {
        $this->storage = (new Collection($posts))->indexBy('id')->all();

        $this->mapper = new StubMapper();
    }

    /**
     * @inheritDoc
     */
    public function save($entity): void
    {
        Assert::isInstanceOf($entity, Post::class);
        $this->storage[$entity['id']] = $entity;
    }

    /**
     * @inheritDoc
     */
    public function remove($entity): void
    {
        Assert::isInstanceOf($entity, Post::class);
        unset($this->storage[$entity['id']]);
    }

    /**
     * @inheritDoc
     */
    public function findByPrimary($primary): Post
    {
        Assert::uuid($primary);

        if (isset($this->storage[$primary])) {
            return $this->storage[$primary];
        }

        throw new PostNotFoundException();
    }

    /**
     * @inheritDoc
     */
    public function findAll(?CriteriaInterface $criteria = null): CollectionInterface
    {
        return new Collection(array_values($this->storage));
    }

    /**
     * @inheritDoc
     */
    public function findOne(?CriteriaInterface $criteria = null): ?Post
    {
        return $this->findAll($criteria)->first();
    }

    /**
     * @inheritDoc
     */
    public function count(?CriteriaInterface $criteria = null): int
    {
        return count($this->findAll($criteria));
    }

    /**
     * @inheritDoc
     */
    public function getMapper(): MapperInterface
    {
        return $this->mapper;
    }

    /**
     * @param mixed $id
     * @return mixed|EntityInterface
     * @deprecated
     * @codeCoverageIgnore
     */
    public function getById($id)
    {
        return $this->findByPrimary($id);
    }

    /**
     * @param mixed $criteria
     * @return CollectionInterface|Post[]
     * @deprecated
     * @codeCoverageIgnore
     */
    public function getList($criteria)
    {
        return $this->findAll($criteria);
    }
}
