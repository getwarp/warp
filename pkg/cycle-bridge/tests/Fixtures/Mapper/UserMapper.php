<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Fixtures\Mapper;

use Warp\Bridge\Cycle\Mapper\HydratorMapper;

class UserMapper extends HydratorMapper
{
    use NextPrimaryKeyUuidTrait;
}
