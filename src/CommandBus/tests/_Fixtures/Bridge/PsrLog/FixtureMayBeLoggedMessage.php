<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\_Fixtures\Bridge\PsrLog;

use spaceonfire\CommandBus\Bridge\PsrLog\MayBeLoggedMessage;
use spaceonfire\CommandBus\Bridge\PsrLog\MayBeLoggedMessageTrait;

class FixtureMayBeLoggedMessage implements MayBeLoggedMessage
{
    use MayBeLoggedMessageTrait;
}
