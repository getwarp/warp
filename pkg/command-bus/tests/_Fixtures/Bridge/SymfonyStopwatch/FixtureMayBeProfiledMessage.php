<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\_Fixtures\Bridge\SymfonyStopwatch;

use spaceonfire\CommandBus\Bridge\SymfonyStopwatch\MayBeProfiledMessageInterface;
use spaceonfire\CommandBus\Bridge\SymfonyStopwatch\MayBeProfiledMessageTrait;

class FixtureMayBeProfiledMessage implements MayBeProfiledMessageInterface
{
    use MayBeProfiledMessageTrait;
}
