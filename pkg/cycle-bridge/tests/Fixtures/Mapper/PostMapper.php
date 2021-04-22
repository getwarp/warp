<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures\Mapper;

use spaceonfire\Bridge\Cycle\Mapper\HydratorMapper;

class PostMapper extends HydratorMapper
{
    use NextPrimaryKeyUuidTrait;
}
