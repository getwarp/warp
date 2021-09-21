<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\Post;

use Cycle\ORM\ORMInterface;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepositoryAdapter;
use spaceonfire\DataSource\Exceptions\NotFoundException;
use spaceonfire\DataSource\Fixtures\Domain\Post\Exceptions;
use spaceonfire\DataSource\Fixtures\Domain\Post\Post;
use spaceonfire\DataSource\Fixtures\Domain\Post\PostRepositoryInterface;

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
