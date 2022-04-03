<?php

declare(strict_types=1);

namespace Warp\DataSource\Fixtures\Infrastructure\Persistence\Post;

use Cycle\ORM\ORMInterface;
use Warp\Criteria\CriteriaInterface;
use Warp\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepositoryAdapter;
use Warp\DataSource\Exceptions\NotFoundException;
use Warp\DataSource\Fixtures\Domain\Post\Exceptions;
use Warp\DataSource\Fixtures\Domain\Post\Post;
use Warp\DataSource\Fixtures\Domain\Post\PostRepositoryInterface;

class CyclePostRepository extends AbstractCycleRepositoryAdapter implements PostRepositoryInterface
{
    public function __construct(ORMInterface $orm)
    {
        parent::__construct('post', $orm);
    }

    /**
     * @inheritDoc
     */
    public function findByPrimary($primary): Post
    {
        /** @var Post $entity */
        $entity = parent::findByPrimary($primary);
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function findOne(?CriteriaInterface $criteria = null): ?Post
    {
        return parent::findOne($criteria);
    }

    protected static function makeNotFoundException($primary = null): NotFoundException
    {
        return new Exceptions\PostNotFoundException(null, compact('primary'));
    }
}
