<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures\Mapper;

use Symfony\Component\Uid\Uuid;

trait NextPrimaryKeyUuidTrait
{
    protected function nextPrimaryKey(): string
    {
        return (string)Uuid::v4();
    }
}
