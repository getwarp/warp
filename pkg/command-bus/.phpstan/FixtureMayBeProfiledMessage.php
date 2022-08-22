<?php

declare(strict_types=1);

namespace Vendor;

use Warp\CommandBus\Middleware\Profiler\MayBeProfiledMessageInterface;
use Warp\CommandBus\Middleware\Profiler\MayBeProfiledMessageTrait;

class FixtureMayBeProfiledMessage implements MayBeProfiledMessageInterface
{
    use MayBeProfiledMessageTrait;
}
