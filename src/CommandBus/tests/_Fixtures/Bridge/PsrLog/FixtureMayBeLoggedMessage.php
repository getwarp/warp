<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\_Fixtures\Bridge\PsrLog;

use spaceonfire\CommandBus\Bridge\PsrLog\MayBeLoggedMessageInterface;
use spaceonfire\CommandBus\Bridge\PsrLog\MayBeLoggedMessageTrait;

class FixtureMayBeLoggedMessage implements MayBeLoggedMessageInterface
{
    use MayBeLoggedMessageTrait;
}
