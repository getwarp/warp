<?php

declare(strict_types=1);

namespace Warp\CommandBus\_Fixtures\Bridge\SymfonyStopwatch;

use Warp\CommandBus\Bridge\SymfonyStopwatch\MayBeProfiledMessageInterface;
use Warp\CommandBus\Bridge\SymfonyStopwatch\MayBeProfiledMessageTrait;

class FixtureMayBeProfiledMessage implements MayBeProfiledMessageInterface
{
    use MayBeProfiledMessageTrait;
}
