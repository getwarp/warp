<?php

declare(strict_types=1);

namespace Warp\CommandBus\_Fixtures\Bridge\PsrLog;

use Warp\CommandBus\Bridge\PsrLog\MayBeLoggedMessageInterface;
use Warp\CommandBus\Bridge\PsrLog\MayBeLoggedMessageTrait;

class FixtureMayBeLoggedMessage implements MayBeLoggedMessageInterface
{
    use MayBeLoggedMessageTrait;
}
