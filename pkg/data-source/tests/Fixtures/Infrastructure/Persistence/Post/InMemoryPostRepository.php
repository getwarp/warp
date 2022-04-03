<?php

declare(strict_types=1);

namespace Warp\DataSource\Fixtures\Infrastructure\Persistence\Post;

use Warp\Collection\Collection;
use Warp\Collection\CollectionInterface;
use Warp\Criteria\CriteriaInterface;
use Warp\DataSource\EntityInterface;
use Warp\DataSource\Fixtures\Domain\Post\Exceptions\PostNotFoundException;
use Warp\DataSource\Fixtures\Domain\Post\Post;
use Warp\DataSource\Fixtures\Domain\Post\PostRepositoryInterface;
use Warp\DataSource\Fixtures\Infrastructure\Mapper\StubMapper;
use Warp\DataSource\MapperInterface;
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
