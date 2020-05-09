<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\Post;

use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Definition\Relation;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\DataSource\Adapters\CycleOrm\Mapper\UuidCycleMapper;
use spaceonfire\DataSource\Adapters\CycleOrm\Repository\AbstractCycleRepository;
use spaceonfire\DataSource\Exceptions\NotFoundException;
use spaceonfire\DataSource\Fixtures\Domain\Post\Exceptions;
use spaceonfire\DataSource\Fixtures\Domain\Post\Post;
use spaceonfire\DataSource\Fixtures\Domain\Post\PostRepositoryInterface;
use spaceonfire\DataSource\Fixtures\Domain\User\User;

class CyclePostRepository extends AbstractCycleRepository implements PostRepositoryInterface
{
    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public static function getTableName(): string
    {
        return 'posts';
    }

    /**
     * @inheritDoc
     */
    public static function getEntityClass(): ?string
    {
        return Post::class;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    protected static function defineInternal(): Entity
    {
        $e = new Entity();
        $e->setRole('post');

        $e->getFields()->set('id', (new Field())->setType('string(36)')->setColumn('id')->setPrimary(true));

        $e->getFields()->set('title', (new Field())->setType('string(255)')->setColumn('title'));

        $e->getFields()->set('authorId', (new Field())->setType('string(36)')->setColumn('authorId'));

        $e->getRelations()->set('author', (new Relation())->setTarget(User::class)->setType('belongsTo'));

        $e->getRelations()->get('author')->getOptions()->set('innerKey', 'authorId');

        $e->setMapper(UuidCycleMapper::class);

        return $e;
    }

    protected static function makeNotFoundException($primary = null): NotFoundException
    {
        return new Exceptions\PostNotFoundException(null, compact('primary'));
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
}
