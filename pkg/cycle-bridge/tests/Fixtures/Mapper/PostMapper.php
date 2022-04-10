<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Fixtures\Mapper;

use Warp\Bridge\Cycle\Mapper\HydratorMapper;

class PostMapper extends HydratorMapper
{
    use NextPrimaryKeyUuidTrait;
}
