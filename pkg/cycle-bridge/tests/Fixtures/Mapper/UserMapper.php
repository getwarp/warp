<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures\Mapper;

use spaceonfire\Bridge\Cycle\Mapper\HydratorMapper;

class UserMapper extends HydratorMapper
{
    use NextPrimaryKeyUuidTrait;
}
