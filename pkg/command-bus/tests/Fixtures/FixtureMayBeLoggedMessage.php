<?php

declare(strict_types=1);

namespace Warp\CommandBus\Fixtures;

use Warp\CommandBus\Middleware\Logger\MayBeLoggedMessageInterface;
use Warp\CommandBus\Middleware\Logger\MayBeLoggedMessageTrait;

class FixtureMayBeLoggedMessage implements MayBeLoggedMessageInterface
{
    use MayBeLoggedMessageTrait;
}
