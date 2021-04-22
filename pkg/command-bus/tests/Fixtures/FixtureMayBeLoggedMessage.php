<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Fixtures;

use spaceonfire\CommandBus\Middleware\Logger\MayBeLoggedMessageInterface;
use spaceonfire\CommandBus\Middleware\Logger\MayBeLoggedMessageTrait;

class FixtureMayBeLoggedMessage implements MayBeLoggedMessageInterface
{
    use MayBeLoggedMessageTrait;
}
