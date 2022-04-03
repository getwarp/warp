<?php

declare(strict_types=1);

namespace Warp\DataSource\Fixtures\Infrastructure\Persistence\Tag;

use Cycle\ORM\ORMInterface;
use Warp\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepositoryAdapter;
use Warp\DataSource\Fixtures\Domain\Tag\TagRepositoryInterface;

class CycleTagRepository extends AbstractCycleRepositoryAdapter implements TagRepositoryInterface
{
    public function __construct(ORMInterface $orm)
    {
        parent::__construct('tag', $orm);
    }
}
