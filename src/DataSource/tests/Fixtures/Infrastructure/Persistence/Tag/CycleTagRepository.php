<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\Tag;

use Cycle\ORM\ORMInterface;
use spaceonfire\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepositoryAdapter;
use spaceonfire\DataSource\Fixtures\Domain\Tag\TagRepository;

class CycleTagRepository extends AbstractCycleRepositoryAdapter implements TagRepository
{
    public function __construct(ORMInterface $orm)
    {
        parent::__construct('tag', $orm);
    }
}
