<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Fixtures;

use spaceonfire\CommandBus\Middleware\Profiler\MayBeProfiledMessageInterface;
use spaceonfire\CommandBus\Middleware\Profiler\MayBeProfiledMessageTrait;

class FixtureMayBeProfiledMessage implements MayBeProfiledMessageInterface
{
    use MayBeProfiledMessageTrait;
}
