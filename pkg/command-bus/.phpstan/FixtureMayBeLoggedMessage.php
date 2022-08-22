<?php

declare(strict_types=1);

namespace Vendor;

use Warp\CommandBus\Middleware\Logger\MayBeLoggedMessageInterface;
use Warp\CommandBus\Middleware\Logger\MayBeLoggedMessageTrait;

class FixtureMayBeLoggedMessage implements MayBeLoggedMessageInterface
{
    use MayBeLoggedMessageTrait;
}
